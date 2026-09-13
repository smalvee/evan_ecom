<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\ShippingCharge;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::latest('orders.created_at')->select('orders.*', 'users.name', 'users.email');
        $query = $query->leftJoin('users', 'users.id', 'orders.user_id');

        if ($request->filled('keyword')) {
            $keyword = $request->get('keyword');
            $query->where(function ($q) use ($keyword) {
                $q->where('users.name', 'like', '%' . $keyword . '%')
                    ->orWhere('users.email', 'like', '%' . $keyword . '%')
                    ->orWhere('users.phone', 'like', '%' . $keyword . '%')
                    ->orWhere('orders.order_id', 'like', '%' . $keyword . '%')
                    ->orWhere('orders.phone', 'like', '%' . $keyword . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('orders.status', $request->get('status'));
        }

        $orders = $query->with('items')->paginate(10)->appends($request->query());

        $data['orders'] = $orders;

        return view('admin.orders.new_list', $data);
    }

    public function destroy($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found.',
            ]);
        }

        // Confirmed or shipped orders must be cancelled instead of deleted,
        // so that stock is restored correctly.
        if (in_array($order->status, ['confirm', 'shipped'], true)) {
            return response()->json([
                'status' => false,
                'message' => 'Confirmed or shipped orders cannot be deleted. Please cancel the order instead.',
            ]);
        }

        DB::beginTransaction();

        try {
            // Remove order items explicitly (do not rely on FK cascade).
            OrderItem::where('order_id', $order->id)->delete();
            $order->delete();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Unable to delete the order.',
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Order deleted successfully.',
        ]);
    }

    /**
     * Toggle an order's paid/unpaid flag (payment_status).
     */
    public function togglePaymentStatus($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found.',
            ]);
        }

        $order->payment_status = $order->payment_status ? 0 : 1;
        $order->save();

        return response()->json([
            'status' => true,
            'payment_status' => (int) $order->payment_status,
            'message' => $order->payment_status ? 'Order marked as paid.' : 'Order marked as unpaid.',
        ]);
    }

    public function details($orderId)
    {
        $order = Order::where('id', $orderId)->first();

        if (!$order) {
            abort(404);
        }

        $orderedItems = OrderItem::with('variant.product')->where('order_id', $order->id)->get();
        $variants = ProductVariant::with('product')->get();

        return view('admin.orders.new_details', [
            'order' => $order,
            'orderedItems' => $orderedItems,
            'variants' => $variants,
        ]);
    }

    public function ChangeOrderStatus($order_id, Request $request)
    {
        $request->validate([
            'status' => 'required|in:pending,confirm,shipped,cancell',
        ]);

        DB::beginTransaction();

        try {
            $order = Order::with('items')->lockForUpdate()->find($order_id);

            if (!$order) {
                DB::rollBack();

                return response()->json([
                    'status' => false,
                    'message' => 'Order not found.',
                ]);
            }

            $this->applyStockForStatusChange($order, $request->status);

            $order->status = $request->status;
            $order->admin_note = $request->admin_note;
            $order->save();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage() ?: 'Unable to update the order status.',
            ]);
        }

        $request->session()->flash('success', 'Status Changed successfully');

        return response()->json([
            'status' => true,
            'message' => 'Status Changed successfully',
        ]);
    }

    /**
     * Adjust a variant's stock by $delta (positive = add, negative = deduct).
     * Locks the row and refuses to go negative.
     *
     * @throws \RuntimeException
     */
    protected function adjustVariantStock($variantId, int $delta): void
    {
        if ($delta === 0) {
            return;
        }

        $variant = ProductVariant::where('id', $variantId)->lockForUpdate()->first();

        if (!$variant) {
            throw new \RuntimeException('Product variant not found.');
        }

        if ($delta < 0) {
            $needed = -$delta;

            if ((int) $variant->qty < $needed) {
                throw new \RuntimeException('Insufficient stock for ' . ($variant->sku ?? ('variant #' . $variantId)) . '.');
            }

            $variant->decrement('qty', $needed);
        } else {
            $variant->increment('qty', $delta);
        }
    }

    /**
     * Deduct stock once when an order reaches "confirmed" (or beyond), and
     * restore it once when a stock-deducted order is cancelled.
     *
     * The stock_deducted flag guarantees the deduction/restoration happens
     * exactly once, even if a status is re-applied.
     */
    protected function applyStockForStatusChange(Order $order, string $newStatus): void
    {
        // Deduct only once, when the order becomes confirmed or shipped.
        if (in_array($newStatus, ['confirm', 'shipped'], true) && !$order->stock_deducted) {
            foreach ($order->items as $item) {
                $this->adjustVariantStock($item->product_id, -(int) $item->qty);
            }
            $order->stock_deducted = true;
        }

        // Restore only once, when cancelling an order whose stock is currently deducted.
        if ($newStatus === 'cancell' && $order->stock_deducted) {
            foreach ($order->items as $item) {
                $this->adjustVariantStock($item->product_id, (int) $item->qty);
            }
            $order->stock_deducted = false;
        }
    }

    public function create_order(Request $request)
    {
        $shippingCharge = ShippingCharge::all();
        // $products = ProductVariant::all();

        $products = ProductVariant::with('product')->get();
        return view('admin.orders.create', compact('products', 'shippingCharge'));
    }

    public function order_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'shipping_method' => 'required',
            'products' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Please fix the errors',
                'errors' => $validator->errors(),
            ]);
        }

        $user = User::where('phone', $request->phone)->first();
        if (!$user) {
            // Generate a fallback email if not provided
            $phone_number = $request->phone;
            $email = !empty($request->email) ? $request->email : 'guest_' . $request->phone . '@example.com';

            $user = new User();
            $user->name = $request->name;
            $user->email = $email;
            $user->phone = $phone_number;
            $user->password = Hash::make('123456');
            $user->save();
        }

        // ✅ STEP 2: Save/Update Customer Address
        CustomerAddress::updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
            ],
        );

        // Compute totals server-side from the submitted items (never trust the client).
        $subtotal = 0;
        $itemDiscountTotal = 0;

        foreach ($request->products as $item) {
            $product = ProductVariant::find($item['id']);
            if (!$product) {
                continue;
            }
            $qty = max(1, (int) $item['qty']);
            $discount = max(0, (float) $item['discount']);
            $subtotal += max(0, ((float) $product->selling_price * $qty) - $discount);
            $itemDiscountTotal += $discount;
        }

        $shipping = max(0, (float) $request->shipping_amount);
        $grandTotal = $subtotal + $shipping;

        // ✅ STEP 4: Create Order
        $order = new Order();
        $order->user_id = $user->id;
        $order->subtotal = $subtotal;
        $order->shipping = $shipping;
        $order->additional_discount = $itemDiscountTotal;
        $order->coupon_code = null;
        $order->discount = 0;
        $order->grand_total = $grandTotal;
        $order->name = $request->name;
        $order->phone = $request->phone;
        $order->address = $request->address;
        $order->notes = $request->order_note;
        $order->save(); // save first to get $order->id

        // Now generate custom order_id like YYYYMMDD + id (e.g., 20251010023)
        $order->order_id = date('Y') . str_pad($order->id, 4, '0', STR_PAD_LEFT);
        $order->save();

        // ✅ STEP 5: Store Order Items from Request
        foreach ($request->products as $item) {
            $product = ProductVariant::with('product')->find($item['id']);
            if (!$product) {
                continue;
            }

            $qty = max(1, (int) $item['qty']);
            $discount = max(0, (float) $item['discount']);

            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $product->id;
            $orderItem->name = $product->sku; // or name
            $orderItem->qty = $qty;
            $orderItem->price = $product->selling_price;
            // Inventory cost snapshot at order time.
            $orderItem->cost_price = $product->average_cost ?? $product->purchase_price ?? 0;
            $orderItem->discount = $discount;
            $orderItem->total = max(0, ((float) $product->selling_price * $qty) - $discount);
            $orderItem->free_delivery = (bool) ($product->product->free_delivery ?? false);
            $orderItem->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Order saved successfully',
            'orderId' => $order->order_id,
        ]);
    }

    public function order_address_update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'f_name' => 'required',
            'address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Please fix the errors',
                'errors' => $validator->errors(),
            ]);
        }

        CustomerAddress::updateOrCreate(
            [
                'user_id' => $request->cus_id,
            ],

            [
                'name' => $request->f_name,
                'address' => $request->address,
            ],
        );

        $order = Order::find($id);

        $order->name = $request->f_name;
        $order->address = $request->address;
        $order->save();

        $request->session()->flash('success', 'Address Changed successfully');

        return response()->json([
            'status' => true,
            'message' => 'Address Changed successfully',
        ]);
    }

    public function order_update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        // Update order totals
        $order->subtotal = $request->subtotal;
        $order->grand_total = $request->subtotal + $order->shipping - $order->discount;
        $order->save();

        // Update order items
        if ($request->has('item_qty')) {
            foreach ($request->item_qty as $itemId => $qty) {
                $orderItem = OrderItem::where('id', $itemId)->where('order_id', $id)->first();

                if ($orderItem) {
                    $orderItem->qty = $qty;
                    $orderItem->total = $orderItem->price * $qty - ($orderItem->discount ?? 0);
                    $orderItem->save();
                }
            }
        }

        $request->session()->flash('success', 'Order updated successfully');

        return response()->json([
            'status' => true,
            'message' => 'Order updated successfully',
        ]);
    }

    /**
     * Single-transaction update for the order detail page.
     *
     * Saves status, customer/order address, existing item edits, item
     * cancellations and newly added items together, applying the order stock
     * rules and rolling back entirely on any failure.
     */
    public function updateOrder(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,confirm,shipped,cancell',
            'f_name' => 'required|string|max:255',
            'address' => 'required|string',
            'item_qty' => 'nullable|array',
            'item_qty.*' => 'required|integer|min:1',
            'item_discount' => 'nullable|array',
            'item_discount.*' => 'nullable|numeric|min:0',
            'cancel_items' => 'nullable|array',
            'cancel_items.*' => 'nullable|integer',
            'new_variant_id' => 'nullable|array',
            'new_variant_id.*' => 'nullable|exists:product_variants,id',
            'new_qty' => 'nullable|array',
            'new_qty.*' => 'nullable|integer|min:1',
            'new_discount' => 'nullable|array',
            'new_discount.*' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ]);
        }

        DB::beginTransaction();

        try {
            $order = Order::with('items')->lockForUpdate()->findOrFail($id);
            $newStatus = $request->status;

            /* 1) Cancel / remove selected items (restore stock once, if deducted). */
            foreach (array_filter((array) $request->cancel_items) as $itemId) {
                $item = OrderItem::where('id', $itemId)->where('order_id', $order->id)->first();

                if (!$item) {
                    continue; // Already removed -> no duplicate stock restoration.
                }

                if ($order->stock_deducted) {
                    $this->adjustVariantStock($item->product_id, (int) $item->qty);
                }

                $item->delete();
            }

            /* 2) Update existing items (qty / discount) and adjust stock by the difference. */
            $itemQtys = (array) $request->item_qty;
            $itemDiscounts = (array) $request->item_discount;

            foreach ($itemQtys as $itemId => $newQty) {
                $item = OrderItem::where('id', $itemId)->where('order_id', $order->id)->first();

                if (!$item) {
                    continue;
                }

                $newQty = (int) $newQty;
                $newDiscount = (float) ($itemDiscounts[$itemId] ?? 0);
                $delta = $newQty - (int) $item->qty;

                if ($order->stock_deducted && $delta !== 0) {
                    // delta > 0 => deduct more; delta < 0 => return the difference.
                    $this->adjustVariantStock($item->product_id, -$delta);
                }

                $item->qty = $newQty;
                $item->discount = $newDiscount;
                $item->total = max(0, ((float) $item->price * $newQty) - $newDiscount);
                $item->save();
            }

            /* 3) Add new items. */
            $newVariantIds = (array) $request->new_variant_id;
            $newQtys = (array) $request->new_qty;
            $newDiscounts = (array) $request->new_discount;

            foreach ($newVariantIds as $i => $variantId) {
                if (empty($variantId)) {
                    continue;
                }

                $qty = (int) ($newQtys[$i] ?? 0);

                if ($qty < 1) {
                    continue;
                }

                $discount = (float) ($newDiscounts[$i] ?? 0);

                $variant = ProductVariant::with('product')->find($variantId);

                if (!$variant) {
                    throw new \RuntimeException('Selected product not found.');
                }

                $price = (float) $variant->selling_price;

                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $variant->id;
                $orderItem->name = $variant->sku;
                $orderItem->qty = $qty;
                $orderItem->price = $price;
                // Inventory cost snapshot at order time.
                $orderItem->cost_price = $variant->average_cost ?? $variant->purchase_price ?? 0;
                $orderItem->discount = $discount;
                $orderItem->total = max(0, ($price * $qty) - $discount);
                $orderItem->free_delivery = (bool) ($variant->product->free_delivery ?? false);
                $orderItem->save();

                // Already-confirmed order: deduct the added quantity now.
                if ($order->stock_deducted) {
                    $this->adjustVariantStock($variant->id, -$qty);
                }
            }

            /* 4) Status-transition stock handling against the current item set. */
            $order->load('items');
            $this->applyStockForStatusChange($order, $newStatus);

            /* 5) Recalculate totals from the current items. */
            $subtotal = (float) $order->items->sum(fn ($it) => (float) $it->total);
            $order->subtotal = $subtotal;
            $order->grand_total = max(0, $subtotal + (float) $order->shipping - (float) $order->discount);

            /* 6) Order + customer fields. */
            $order->status = $newStatus;
            $order->admin_note = $request->admin_note;
            $order->name = $request->f_name;
            $order->address = $request->address;
            $order->save();

            /* 7) Keep the saved customer address in sync. */
            CustomerAddress::updateOrCreate(
                ['user_id' => $order->user_id],
                [
                    'name' => $request->f_name,
                    'phone' => $order->phone,
                    'address' => $request->address,
                ]
            );

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage() ?: 'Unable to update the order.',
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Order updated successfully.',
        ]);
    }
}
