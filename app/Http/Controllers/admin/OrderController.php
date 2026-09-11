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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::latest('orders.created_at')->select('orders.*', 'users.name', 'users.email');
        $orders = $orders->leftJoin('users', 'users.id', 'orders.user_id');

        if ($request->get('keyword') != '') {
            $orders = $orders->where('users.name', 'like', '%' . $request->keyword . '%');
            $orders = $orders->orwhere('users.email', 'like', '%' . $request->keyword . '%');
            $orders = $orders->orwhere('users.phone', 'like', '%' . $request->keyword . '%');
            $orders = $orders->orwhere('orders.order_id', 'like', '%' . $request->keyword . '%');
        }

        $orders = $orders->with('items')->paginate(10);

        $data['orders'] = $orders;

        return view('admin.orders.new_list', $data);
    }

    public function details($orderId)
    {
        $order = Order::where('id', $orderId)->first();
        $orderedItems = OrderItem::where('order_id', $order->id)->get();

        $data = [];
        $data['order'] = $order;
        $data['orderedItems'] = $orderedItems;
        return view('admin.orders.new_details', $data);
    }

    public function ChangeOrderStatus($order_id, Request $request)
    {
        $order = Order::find($order_id);

        $order->status = $request->status;
        $order->admin_note = $request->admin_note;
        $order->save();

        $request->session()->flash('success', 'Status Changed successfully');

        return response()->json([
            'status' => true,
            'message' => 'Status Changed successfully',
        ]);
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

        // ✅ STEP 4: Create Order
        $order = new Order();
        $order->user_id = $user->id;
        $order->subtotal = $request->sum_subTotal;
        $order->shipping = $request->shipping_amount;
        $order->additional_discount = $request->discount_amount;
        $order->coupon_code = null;
        $order->discount = 0;
        $order->grand_total = $request->total_amount;
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

            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $product->id;
            $orderItem->name = $product->sku; // or name
            $orderItem->qty = $item['qty'];
            $orderItem->price = $product->selling_price;
            $orderItem->discount = $item['discount'];
            $orderItem->total = $product->selling_price * $item['qty'] - $item['discount'];
            $orderItem->free_delivery = (bool) ($product->product->free_delivery ?? false);
            $orderItem->save();

            // ✅ Decrease stock
            $product->decrement('qty', $item['qty']);
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
}
