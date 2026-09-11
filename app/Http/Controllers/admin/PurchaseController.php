<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\NewProduct;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItems;
use App\Models\Supplier;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::latest('id')->with('variant.product');

        if (!empty($request->get('keyword'))) {
            $keyword = $request->get('keyword');

            $query
                ->leftJoin('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
                ->where(function ($q) use ($keyword) {
                    $q->where('purchases.id', 'like', "%{$keyword}%")->orWhere('suppliers.name', 'like', "%{$keyword}%");
                })
                ->select('purchases.*');
        }

        // paginate AFTER filters
        $purchase = $query->paginate(10);

        return view('admin.purchase.list', compact('purchase'));
    }

    public function return_list(Request $request)
    {
        $query = PurchaseReturn::latest('id')->with('variant.product');

        if (!empty($request->get('keyword'))) {
            $keyword = $request->get('keyword');

            $query->whereHas('variant', function ($q) use ($keyword) {
                $q->where('sku', 'like', '%' . $keyword . '%')->orWhereHas('product', function ($p) use ($keyword) {
                    $p->where('name', 'like', '%' . $keyword . '%');
                });
            });
        }

        // paginate AFTER filters
        $purchase_return = $query->paginate(10);

        return view('admin.purchase.return_list', compact('purchase_return'));
    }

    public function return_view($id)
    {
        $purchase_return_list = PurchaseReturnItems::where('purchase_return_id', $id)->get();
        return view('admin.purchase.return_view', compact('purchase_return_list'));
    }
    public function create()
    {
        $product_sku = ProductVariant::latest('id')->with('product')->get();
        // dd($product_sku);
        $supplier = Supplier::all();
        return view('admin.purchase.create', compact('product_sku', 'supplier'));
    }

    public function store(Request $request)
    {
        $rules = [
            'supplier_id' => 'required',
            'total_purchase' => 'required',
            'date' => 'required',
            'p_name' => 'required',
            'variant_id' => 'required|array',
            'qty' => 'required|array',
            'unit_cost' => 'required|array',
            'profit_amount' => 'required|array',
            'discount' => 'required|array',
            'selling_price' => 'required|array',
            'mrp' => 'required|array', // MRP
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

        $purchase = new Purchase();
        $purchase->supplier_id = $request->supplier_id;
        $purchase->total = $request->total_purchase;
        $purchase->date = $request->date;
        $purchase->save();

        foreach ($request->variant_id as $index => $variantId) {
            $qty = (int) $request->qty[$index];
            $unitCost = (float) $request->unit_cost[$index];
            $profitAmount = (float) $request->profit_amount[$index];
            $discount = (float) $request->discount[$index];
            $pname = $request->p_name[$index];

            // 🔹 Calculations (trusted on backend)
            $baseAmount = $unitCost;
            $mrp = $baseAmount + $profitAmount;
            $selling = $baseAmount + $profitAmount - $discount;

            if ($selling < 0) {
                $selling = 0;
            }

            // 🔹 Save purchase row
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

            // 🔹 Update stock qty
            $variant = ProductVariant::find($variantId);
            $previousQty = $variant ? $variant->qty : 0;

            ProductVariant::updateOrCreate(
                ['id' => $variantId],
                [
                    'purchase_price' => $unitCost,
                    'selling_price' => $selling,
                    'compare_price' => $mrp, // ✅ MRP
                    'qty' => $previousQty + $qty,
                ],
            );
        }

        return response()->json([
            'status' => true,
            'message' => 'Purchase saved successfully',
        ]);
    }

    public function edit($id)
    {
        $product_sku = ProductVariant::latest('id')->with('product')->get();
        // dd($product_sku);
        $supplier = Supplier::all();

        $purchase_info = Purchase::where('id', $id)->first();
        $purchaseItems = PurchaseItem::where('purchase_id', $purchase_info->id)->get();

        $supplier_info = Supplier::where('id', $purchase_info->supplier_id)->first();
        $variant_info = ProductVariant::where('id', $purchase_info->variant_id)->first();
        return view('admin.purchase.edit', compact('product_sku', 'supplier', 'purchase_info', 'supplier_info', 'variant_info', 'purchaseItems'));
    }

    public function update($id, Request $request)
    {
        $rules = [
            'supplier_id' => 'required',
            'total_purchase' => 'required',
            'date' => 'required',
            'variant_id' => 'required|array',
            'item_id' => 'required|array',
            'unit_cost' => 'required|array',
            'profit_amount' => 'required|array',
            'discount' => 'required|array',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

        /* =======================
       Update Purchase
    ======================= */
        $purchase = Purchase::findOrFail($id);
        $purchase->supplier_id = $request->supplier_id;
        $purchase->total = $request->total_purchase;
        $purchase->date = $request->date;
        $purchase->save();

        /* =======================
       Update Items (NO QTY)
    ======================= */
        foreach ($request->variant_id as $index => $variantId) {
            $itemId = $request->item_id[$index];
            $unitCost = (float) $request->unit_cost[$index];
            $profitAmount = (float) $request->profit_amount[$index];
            $discount = (float) $request->discount[$index];

            // 🔹 Backend calculations
            $mrp = $unitCost + $profitAmount;
            $selling = $unitCost + $profitAmount - $discount;
            if ($selling < 0) {
                $selling = 0;
            }

            /* =======================
           Update Purchase Item
        ======================= */
            $purchaseItem = PurchaseItem::findOrFail($itemId);
            $purchaseItem->unit_cost = $unitCost;
            $purchaseItem->profit_margin = $profitAmount;
            $purchaseItem->discount = $discount;
            $purchaseItem->selling_price = $selling;
            $purchaseItem->date = $request->date;
            $purchaseItem->save();

            /* =======================
           Update Variant Prices
        ======================= */
            $variant = ProductVariant::find($variantId);
            if ($variant) {
                $variant->purchase_price = $unitCost;
                $variant->selling_price = $selling;
                $variant->compare_price = $mrp; // MRP
                $variant->save();
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Purchase updated successfully',
        ]);
    }

    public function purchase_return(Request $request)
    {
        $product_sku = ProductVariant::latest('id')->with('product')->get();
        // dd($product_sku);
        $supplier = Supplier::all();
        return view('admin.purchase.return', compact('product_sku', 'supplier'));
    }

    public function search(Request $request)
    {
        $purchaseId = $request->purchase_id;

        $purchase = Purchase::with(['items.variant'])->find($purchaseId);

        if (!$purchase) {
            return response()->json([
                'status' => false,
                'message' => 'Purchase not found',
            ]);
        }

        return response()->json([
            'status' => true,
            'purchase' => $purchase,
        ]);
    }

    public function return_store(Request $request)
    {
        DB::beginTransaction();

        try {
            if (!$request->has('return_qty')) {
                return response()->json([
                    'status' => false,
                    'message' => 'No items selected for return',
                ]);
            }

            // Create return master ONCE
            $p_return = PurchaseReturn::create([
                'purchase_id' => $request->purchase_id,
                'return_amount' => 0,
            ]);

            $total_return_amount = 0;

            foreach ($request->return_qty as $variantId => $returnQty) {
                if ($returnQty <= 0) {
                    continue;
                }

                $variant = ProductVariant::find($variantId);

                if (!$variant) {
                    continue;
                }

                if ($returnQty > $variant->qty) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => 'Return quantity exceeds current stock',
                    ]);
                }

                // Reduce stock
                $variant->qty -= $returnQty;
                $variant->save();

                // Calculate amount
                $lineAmount = $returnQty * $variant->purchase_price;
                $total_return_amount += $lineAmount;

                // Save return item
                PurchaseReturnItems::create([
                    'purchase_return_id' => $p_return->id,
                    'variant_id' => $variantId,
                    'qty' => $returnQty,
                    'unit_cost' => $variant->purchase_price,
                ]);
            }

            // Update total return amount
            $p_return->return_amount = $total_return_amount;
            $p_return->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Selected items returned successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
