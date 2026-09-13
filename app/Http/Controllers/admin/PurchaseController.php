<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItems;
use App\Models\Supplier;
use App\Services\InventoryCostingService;
use App\Services\ProductVariantPricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PurchaseController extends Controller
{
    protected ProductVariantPricingService $pricing;
    protected InventoryCostingService $costing;

    public function __construct(ProductVariantPricingService $pricing, InventoryCostingService $costing)
    {
        $this->pricing = $pricing;
        $this->costing = $costing;
    }

    public function index(Request $request)
    {
        $query = Purchase::latest('id')->with('supplier');

        if (!empty($request->get('keyword'))) {
            $keyword = $request->get('keyword');

            $query->where(function ($q) use ($keyword) {
                $q->where('purchases.id', 'like', "%{$keyword}%")
                    ->orWhereHas('supplier', function ($s) use ($keyword) {
                        $s->where('name', 'like', "%{$keyword}%");
                    });
            });
        }

        $purchase = $query->paginate(10)->appends($request->query());

        return view('admin.purchase.list', compact('purchase'));
    }

    public function return_list(Request $request)
    {
        $query = PurchaseReturn::latest('id')->with(['purchase.supplier', 'items.variant.product']);

        if (!empty($request->get('keyword'))) {
            $keyword = $request->get('keyword');

            $query->where(function ($q) use ($keyword) {
                $q->where('purchase_returns.id', 'like', "%{$keyword}%")
                    ->orWhereHas('purchase.supplier', function ($s) use ($keyword) {
                        $s->where('name', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('items.variant', function ($v) use ($keyword) {
                        $v->where('sku', 'like', "%{$keyword}%")
                            ->orWhereHas('product', function ($p) use ($keyword) {
                                $p->where('name', 'like', "%{$keyword}%");
                            });
                    });
            });
        }

        $purchase_return = $query->paginate(10)->appends($request->query());

        return view('admin.purchase.return_list', compact('purchase_return'));
    }

    public function return_view($id)
    {
        $purchase_return = PurchaseReturn::with(['items.variant.product', 'purchase.supplier'])->findOrFail($id);
        $purchase_return_list = $purchase_return->items;

        return view('admin.purchase.return_view', compact('purchase_return_list', 'purchase_return'));
    }

    public function create()
    {
        $product_sku = ProductVariant::latest('id')->with('product')->get();
        $supplier = Supplier::all();

        return view('admin.purchase.create', compact('product_sku', 'supplier'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,id',
            'date' => 'required|date',
            'p_name' => 'required|array',
            'variant_id' => 'required|array|min:1',
            'variant_id.*' => 'required|distinct|exists:product_variants,id',
            'qty' => 'required|array',
            'qty.*' => 'required|integer|min:1',
            'unit_cost' => 'required|array',
            'unit_cost.*' => 'required|numeric|min:0',
            'profit_amount' => 'required|array',
            'profit_amount.*' => 'required|numeric|min:0',
            'discount' => 'required|array',
            'discount.*' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

        try {
            DB::transaction(function () use ($request) {
                $purchase = new Purchase();
                $purchase->supplier_id = $request->supplier_id;
                $purchase->date = $request->date;
                $purchase->total = 0;
                $purchase->save();

                $total = 0;

                foreach ($request->variant_id as $index => $variantId) {
                    $qty = (int) $request->qty[$index];
                    $unitCost = (float) $request->unit_cost[$index];
                    $profitAmount = (float) $request->profit_amount[$index];
                    $discount = (float) $request->discount[$index];
                    $pname = $request->p_name[$index];

                    // Server-side pricing calculations.
                    $mrp = $unitCost + $profitAmount;
                    $selling = max(0, $unitCost + $profitAmount - $discount);

                    $purchaseItem = new PurchaseItem();
                    $purchaseItem->purchase_id = $purchase->id;
                    $purchaseItem->date = $request->date;
                    $purchaseItem->variant_id = $variantId;
                    $purchaseItem->p_name = $pname;
                    $purchaseItem->qty = $qty;
                    $purchaseItem->unit_cost = $unitCost;
                    $purchaseItem->profit_margin = $profitAmount;
                    $purchaseItem->discount = $discount;
                    $purchaseItem->selling_price = $selling;
                    $purchaseItem->save();

                    // Purchase total is derived from the items (never trusted from the request).
                    $total += $qty * $unitCost;

                    $variant = ProductVariant::find($variantId);
                    if ($variant) {
                        // Stock + weighted-average inventory cost.
                        $this->costing->recordPurchase($variant, $qty, $unitCost);

                        // Purchase cost + initial/current pricing. A manually managed
                        // price is preserved (only cost and stock are updated).
                        $this->pricing->applyPurchasePricing(
                            $variant,
                            $unitCost,
                            $profitAmount,
                            $discount,
                            'Purchase #' . $purchase->id
                        );
                    }
                }

                $purchase->total = $total;
                $purchase->save();
            });
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unable to save the purchase. Please try again.',
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Purchase saved successfully',
        ]);
    }

    public function edit($id)
    {
        $product_sku = ProductVariant::latest('id')->with('product')->get();
        $supplier = Supplier::all();

        $purchase_info = Purchase::with(['items.variant.product', 'supplier'])->findOrFail($id);
        $purchaseItems = $purchase_info->items;
        $supplier_info = $purchase_info->supplier;
        $variant_info = $purchaseItems->first()?->variant;

        return view('admin.purchase.edit', compact('product_sku', 'supplier', 'purchase_info', 'supplier_info', 'variant_info', 'purchaseItems'));
    }

    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,id',
            'date' => 'required|date',
            'item_id' => 'required|array',
            'item_id.*' => 'required|exists:purchase_items,id',
            // variant_id is only used to sync variant pricing; it may be absent if the
            // linked variant was removed, so it is validated leniently here.
            'variant_id' => 'nullable|array',
            'variant_id.*' => 'nullable|integer',
            'unit_cost' => 'required|array',
            'unit_cost.*' => 'required|numeric|min:0',
            'profit_amount' => 'required|array',
            'profit_amount.*' => 'required|numeric|min:0',
            'discount' => 'required|array',
            'discount.*' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

        // Explicit opt-in: only recalculate the current selling price / MRP when the
        // admin checks "Update Current Product Price". Default is NOT to change it.
        $updatePrice = $request->boolean('update_price');
        $adminId = Auth::guard('admin')->id() ?? Auth::id();

        try {
            DB::transaction(function () use ($id, $request, $updatePrice, $adminId) {
                $purchase = Purchase::findOrFail($id);
                $purchase->supplier_id = $request->supplier_id;
                $purchase->date = $request->date;
                $purchase->save();

                $total = 0;

                // Items are updated WITHOUT changing quantities (existing behaviour).
                $variantIds = (array) $request->variant_id;

                foreach ((array) $request->item_id as $index => $itemId) {
                    $unitCost = (float) ($request->unit_cost[$index] ?? 0);
                    $profitAmount = (float) ($request->profit_amount[$index] ?? 0);
                    $discount = (float) ($request->discount[$index] ?? 0);

                    $mrp = $unitCost + $profitAmount;
                    $selling = max(0, $unitCost + $profitAmount - $discount);

                    $purchaseItem = PurchaseItem::where('id', $itemId)
                        ->where('purchase_id', $purchase->id)
                        ->first();

                    if (!$purchaseItem) {
                        continue;
                    }

                    $purchaseItem->unit_cost = $unitCost;
                    $purchaseItem->profit_margin = $profitAmount;
                    $purchaseItem->discount = $discount;
                    $purchaseItem->selling_price = $selling;
                    $purchaseItem->date = $request->date;
                    $purchaseItem->save();

                    $total += $purchaseItem->qty * $unitCost;

                    $variantId = $variantIds[$index] ?? $purchaseItem->variant_id;
                    $variant = $variantId ? ProductVariant::find($variantId) : null;
                    if ($variant) {
                        if ($updatePrice) {
                            // Admin explicitly chose to update current pricing.
                            $this->pricing->applyPurchasePricing(
                                $variant,
                                $unitCost,
                                $profitAmount,
                                $discount,
                                'Purchase #' . $purchase->id . ' price update',
                                $adminId,
                                true
                            );
                        } else {
                            // Default: cost only. Current MRP / selling price is preserved.
                            $this->pricing->applyPurchaseCost($variant, $unitCost);
                        }

                        // Inventory average cost is independent of commercial pricing.
                        $this->costing->recalculateAverageCost($variant);
                    }
                }

                $purchase->total = $total;
                $purchase->save();
            });
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unable to update the purchase. Please try again.',
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Purchase updated successfully',
        ]);
    }

    public function purchase_return(Request $request)
    {
        $product_sku = ProductVariant::latest('id')->with('product')->get();
        $supplier = Supplier::all();

        return view('admin.purchase.return', compact('product_sku', 'supplier'));
    }

    public function search(Request $request)
    {
        $purchase = Purchase::with(['items.variant.product'])->find($request->purchase_id);

        if (!$purchase) {
            return response()->json([
                'status' => false,
                'message' => 'Purchase not found',
            ]);
        }

        // Quantity already returned per variant for this purchase.
        $returned = PurchaseReturnItems::whereIn('purchase_return_id', function ($q) use ($purchase) {
            $q->select('id')->from('purchase_returns')->where('purchase_id', $purchase->id);
        })
            ->selectRaw('variant_id, SUM(qty) as returned_qty')
            ->groupBy('variant_id')
            ->pluck('returned_qty', 'variant_id');

        $purchase->items->each(function ($item) use ($returned) {
            $returnedQty = (int) ($returned[$item->variant_id] ?? 0);
            $item->returned_qty = $returnedQty;
            $item->remaining_qty = max(0, (int) $item->qty - $returnedQty);
        });

        return response()->json([
            'status' => true,
            'purchase' => $purchase,
        ]);
    }

    public function return_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'purchase_id' => 'required|exists:purchases,id',
            'return_qty' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

        $purchase = Purchase::findOrFail($request->purchase_id);

        DB::beginTransaction();

        try {
            $pReturn = PurchaseReturn::create([
                'purchase_id' => $purchase->id,
                'return_amount' => 0,
            ]);

            $totalReturnAmount = 0;
            $hasItems = false;

            foreach ($request->return_qty as $variantId => $returnQty) {
                $returnQty = (int) $returnQty;

                if ($returnQty <= 0) {
                    continue;
                }

                // The item must belong to this purchase.
                $purchaseItem = PurchaseItem::where('purchase_id', $purchase->id)
                    ->where('variant_id', $variantId)
                    ->first();

                if (!$purchaseItem) {
                    DB::rollBack();

                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid item selected for return.',
                    ]);
                }

                // How much of this purchase item has already been returned.
                $alreadyReturned = PurchaseReturnItems::where('variant_id', $variantId)
                    ->whereIn('purchase_return_id', function ($q) use ($purchase) {
                        $q->select('id')->from('purchase_returns')->where('purchase_id', $purchase->id);
                    })
                    ->sum('qty');

                $remaining = (int) $purchaseItem->qty - (int) $alreadyReturned;

                if ($returnQty > $remaining) {
                    DB::rollBack();

                    return response()->json([
                        'status' => false,
                        'message' => "Return quantity exceeds the purchased quantity (remaining: {$remaining}).",
                    ]);
                }

                $variant = ProductVariant::find($variantId);

                if (!$variant) {
                    DB::rollBack();

                    return response()->json([
                        'status' => false,
                        'message' => 'Product variant not found.',
                    ]);
                }

                if ($returnQty > (int) $variant->qty) {
                    DB::rollBack();

                    return response()->json([
                        'status' => false,
                        'message' => 'Return quantity exceeds current stock.',
                    ]);
                }

                // Value the return at the ORIGINAL purchase cost.
                $unitCost = (float) $purchaseItem->unit_cost;

                $lineAmount = $returnQty * $unitCost;
                $totalReturnAmount += $lineAmount;

                PurchaseReturnItems::create([
                    'purchase_return_id' => $pReturn->id,
                    'variant_id' => $variantId,
                    'qty' => $returnQty,
                    'unit_cost' => $unitCost,
                ]);

                // Decrease stock and recalculate the weighted-average cost
                // (now that the return is recorded).
                $this->costing->applyReturn($variant, $returnQty);

                $hasItems = true;
            }

            if (!$hasItems) {
                DB::rollBack();

                return response()->json([
                    'status' => false,
                    'message' => 'No items selected for return.',
                ]);
            }

            $pReturn->return_amount = $totalReturnAmount;
            $pReturn->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Purchase return saved successfully.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Unable to save the purchase return. Please try again.',
            ]);
        }
    }
}
