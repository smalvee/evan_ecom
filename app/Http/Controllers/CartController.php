<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CustomerAddress;
use App\Models\DiscountCoupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ShippingCharge;
use App\Models\User;
use Gloudemans\Shoppingcart\Facades\Cart;
use GuzzleHttp\Psr7\Message;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Hash;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'qty' => 'required|integer|min:1',
        ]);
        $product = DB::table('product_variants as pv')->leftJoin('product_images as pi', 'pi.product_id', '=', 'pv.id')->leftJoin('new_products as np', 'np.id', '=', 'pv.product_id')->select('pv.id', 'pv.sku', 'pv.selling_price', 'pv.qty', 'pi.image', 'np.free_delivery')->where('pv.id', $request->id)->orderBy('pi.sort_order', 'asc')->first();

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found',
            ]);
        }

        // Stock check (skip when the variant's qty is not tracked / null).
        $availableQty = ($product->qty === null || $product->qty === '') ? null : (int) $product->qty;

        if ($availableQty !== null && $request->qty > $availableQty) {
            return response()->json([
                'status' => false,
                'message' => 'Only ' . $availableQty . ' items in stock',
            ]);
        }

        // Check if product already exists in cart
        $exists = Cart::search(function ($cartItem) use ($product) {
            return $cartItem->id == $product->id;
        })->isNotEmpty();

        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => $product->sku . ' already added to cart',
                'cartCount' => Cart::count(),
            ]);
        }

        // Add to cart using fields from DB query
        Cart::add(
            $product->id,
            $product->sku, // Using sku as title
            $request->qty,
            $product->selling_price,
            [
                'productImage' => $product->image,
                'freeDelivery' => (int) ($product->free_delivery ?? 0),
            ],
        );

        // Render updated cart sidebar
        $cartView = view('front.layouts.card-sidebar', [
            'cartContent' => Cart::content(),
        ])->render();

        $pop_uo_cartView = view('front.layouts.cart-pop-up', [
            'cartContent' => Cart::content(),
        ])->render();

        return response()->json([
            'status' => true,
            'message' => $product->sku . ' added to cart',
            'cartView' => $cartView,
            'pop_uo_cartView' => $pop_uo_cartView,
            'cartCount' => Cart::count(),
            'swalType' => 'success', // optional, could be 'error'
        ]);
    }

    public function updateCart(Request $request)
    {
        $rowId = $request->rowId;
        $qty = $request->qty;

        $itemInfo = Cart::get($rowId);
        $product = ProductVariant::find($itemInfo->id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found',
            ]);
        }

        $availableQty = $product->qty !== null ? (int) $product->qty : null;

        if ($availableQty !== null && $qty > $availableQty) {
            $message = 'Requested quantity (' . $qty . ') not available. Only ' . $availableQty . ' items are available';
            $status = false;
            session()->flash('error', $message);
        } else {
            Cart::update($rowId, $qty);
            $message = 'Cart updated successfully';
            $status = true;
            session()->flash('success', $message);
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
        ]);
    }

    public function deleteItem(Request $request)
    {
        $rowId = $request->rowId;

        $itemInfo = Cart::get($rowId);

        if ($itemInfo == null) {
            return response()->json([
                'status' => false,
                'message' => 'Item not found in cart',
            ]);
        }

        Cart::remove($rowId);

        // Destroy cart if empty
        if (Cart::content()->isEmpty()) {
            Cart::destroy();
        }

        // Render updated cart sidebar
        $cartView = view('front.layouts.card-sidebar', [
            'cartContent' => Cart::content(),
        ])->render();

        $pop_uo_cartView = view('front.layouts.cart-pop-up', [
            'cartContent' => Cart::content(),
        ])->render();

        return response()->json([
            'status' => true,
            'message' => 'Item removed successfully',
            'cartView' => $cartView,
            'pop_uo_cartView' => $pop_uo_cartView,
            'cartCount' => Cart::count(),
        ]);
    }

    public function deleteItemFromSideCart(Request $request)
    {
        $rowId = $request->rowId;

        $itemInfo = Cart::get($rowId);

        if ($itemInfo == null) {
            $errorMessage = 'Item not found in cart';
            session()->flash('error', $errorMessage);

            return response()->json([
                'status' => false,
                'message' => $errorMessage,
            ]);
        }

        Cart::remove($rowId);
        $successMessage = 'Item removed Successfully';

        session()->flash('success', $successMessage);

        $cartView = view('front.layouts.card-sidebar', [
            'cartContent' => Cart::content(),
        ])->render();

        // Clean up phantom/empty session items
        if (Cart::content()->isEmpty()) {
            Cart::destroy();
        }

        // Correct cart count
        $cart_count = Cart::content()->isEmpty() ? 0 : Cart::content()->count();

        return response()->json([
            'status' => true,
            'message' => $successMessage,
            'cartView' => $cartView,
            'cartCount' => $cart_count,
        ]);
    }

    public function processCheckout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'district' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Please fix the errors',
                'errors' => $validator->errors(),
            ]);
        }

        // Validate stock availability for every cart line before placing the order.
        foreach (Cart::content() as $cartItem) {
            $stockVariant = ProductVariant::find($cartItem->id);

            if (
                $stockVariant
                && $stockVariant->qty !== null
                && $stockVariant->qty !== ''
                && (int) $stockVariant->qty < (int) $cartItem->qty
            ) {
                return response()->json([
                    'status' => false,
                    'message' => 'Insufficient stock for ' . ($stockVariant->sku ?? 'item') . '.',
                ]);
            }
        }

        // ✅ STEP 1: Get or Create User
        $user = Auth::user();

        if (!$user) {
            // Check by phone
            $user = User::where('phone', $request->phone)->first();

            if (!$user) {
                // Generate a fallback email if not provided
                $phone_number = $request->phone;
                $email = !empty($request->email) ? $request->email : 'guest_' . $request->phone . '@example.com';

                // $user = User::create([
                //     'name' => $request->name,
                //     'phone' => $email,
                //     'email' => $email,
                //     'role' => 1,
                //     'password' => Hash::make('123456'),
                // ]);

                $user = new User();
                $user->name = $request->name;
                $user->email = $email;
                $user->phone = $phone_number;
                // Guest accounts get a random, unguessable password. They cannot log in
                // with it; registering with the same phone claims the account instead.
                $user->password = Hash::make(Str::random(40));
                $user->save();
            }
        }

        // ✅ STEP 2: Save/Update Customer Address
        CustomerAddress::updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                // 'shipping' => $request->shipping_method,
            ],
        );

        // ✅ STEP 3: Calculate amounts
        $subTotal = (float) Cart::subtotal(2, '.', '');
        $shipping = ShippingCharge::rateForDistrict($request->district);

        if (Order::isFreeDeliveryCart(Cart::content())) {
            $shipping = 0;
        }

        $discount = 0;
        $couponCode = null;

        if (!empty($request->coupon)) {
            $coupon = DiscountCoupon::where('code', $request->coupon)
                ->where('status', 1)
                ->where('expires_at', '>=', now())
                ->where(function ($q) {
                    $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
                })
                ->first();

            if ($coupon && $subTotal >= $coupon->min_amount) {
                $valid = true;

                if ($coupon->max_uses > 0 && $coupon->orders()->count() >= $coupon->max_uses) {
                    $valid = false;
                }

                if ($coupon->max_uses_user > 0 && $coupon->orders()->where('user_id', $user->id)->count() >= $coupon->max_uses_user) {
                    $valid = false;
                }

                if ($valid) {
                    $discount = $coupon->type === 'percent' ? ($subTotal * $coupon->discount_amount) / 100 : $coupon->discount_amount;

                    if ($discount > $subTotal) {
                        $discount = $subTotal;
                    }

                    $couponCode = $coupon->code;
                }
            }
        }

        $grandTotal = $subTotal + $shipping - $discount;

        try {
            DB::beginTransaction();

            // ✅ STEP 4: Create Order
            $order = new Order();
            $order->user_id = $user->id;
            $order->subtotal = $subTotal;
            $order->shipping = $shipping;
            $order->discount = $discount;
            $order->coupon_code = $couponCode;
            $order->grand_total = $grandTotal;
            $order->name = $request->name;
            $order->phone = $request->phone;
            $order->address = $request->address;
            $order->notes = $request->order_note;
            $order->save(); // save first to get $order->id

            // Now generate custom order_id like YYYYMMDD + id (e.g., 20251010023)
            $order->order_id = date('Y') . str_pad($order->id, 4, '0', STR_PAD_LEFT);
            $order->save();

            // ✅ STEP 5: Store Order Items
            foreach (Cart::content() as $item) {
                $orderItem = new OrderItem();
                $orderItem->product_id = $item->id;
                $orderItem->order_id = $order->id;
                $orderItem->name = $item->name;
                $orderItem->qty = $item->qty;
                $orderItem->price = $item->price;
                // Inventory cost snapshot at order time (for accurate historical COGS).
                $variant = ProductVariant::find($item->id);
                $orderItem->cost_price = $variant ? ($variant->average_cost ?? $variant->purchase_price ?? 0) : 0;
                $orderItem->total = $item->price * $item->qty;
                $orderItem->free_delivery = (int) ($item->options->freeDelivery ?? 0);
                $orderItem->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again.',
            ]);
        }

        // ✅ STEP 6: Clear Cart
        Cart::destroy();

        return response()->json([
            'status' => true,
            'message' => 'Order saved successfully',
            'orderId' => $order->order_id,
        ]);
    }

    public function applyCoupon(Request $request)
    {
        $couponCode = $request->coupon;
        $subTotal = (float) $request->subtotal;

        $coupon = DiscountCoupon::where('code', $couponCode)
            ->where('status', 1)
            ->where('expires_at', '>=', now())
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->first();

        if (!$coupon) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired coupon code.',
            ]);
        }

        $discount = 0;

        // Check minimum amount
        if ($subTotal < (float) $coupon->min_amount) {
            return response()->json([
                'status' => false,
                'message' => 'Subtotal does not meet the minimum amount required for this coupon.',
            ]);
        }

        // Check maximum uses
        if ($coupon->max_uses > 0 && $coupon->orders()->count() >= $coupon->max_uses) {
            return response()->json([
                'status' => false,
                'message' => 'This coupon has reached its maximum number of uses.',
            ]);
        }

        // Check per-user maximum uses
        $currentUser = Auth::user();
        if (
            $coupon->max_uses_user > 0
            && $currentUser
            && $coupon->orders()->where('user_id', $currentUser->id)->count() >= $coupon->max_uses_user
        ) {
            return response()->json([
                'status' => false,
                'message' => 'You have reached the maximum number of uses for this coupon.',
            ]);
        }

        if ($coupon->type === 'fixed') {
            $discount = $coupon->discount_amount;
        } elseif ($coupon->type === 'percent') {
            $discount = ($subTotal * $coupon->discount_amount) / 100;
        }

        // Ensure discount does not exceed subtotal
        if ($discount > $subTotal) {
            $discount = $subTotal;
        }

        return response()->json([
            'status' => true,
            'discount' => (float) $discount,
            'coupon' => $coupon->code,
            'message' => 'Coupon applied successfully.',
        ]);
    }

    public function singleCart(Request $request, $id)
    {
        $cartContent = Cart::content();
        $categories = Category::latest('id')->get();

        $user = Auth::user();
        $customerAddress = null; // default value

        if ($user) {
            $customerAddress = CustomerAddress::where('user_id', $user->id)->first();
            // dd($customerAddress); // only for debugging
        }

        $shippingCharge = ShippingCharge::all();

        $selected_products = ProductVariant::with('product')->where('id', $id)->get();

        $selectedFreeDelivery = false;
        if ($selected_products->isNotEmpty()) {
            $selectedFreeDelivery = (bool) ($selected_products->first()->product->free_delivery ?? false);
        }

        $selected_qty = $request->qty;

        if ($request->qty == null) {
            $selected_qty = 1;
        }

        $data['cartContent'] = $cartContent;
        $data['customerAddress'] = $customerAddress;
        $data['shippingCharge'] = $shippingCharge;
        $data['categories'] = $categories;
        $data['selected_products'] = $selected_products;
        $data['selected_qty'] = $selected_qty;
        $data['selectedFreeDelivery'] = $selectedFreeDelivery;
        $data['districtAmounts'] = ShippingCharge::ratesByDistrict();

        return view('front.pages.new_single_checkout', $data);
    }

    public function singleCheckout($slug, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'district' => 'required',
            'product_id' => 'required|exists:product_variants,id',
            'selected_qty' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Please fix the errors',
                'errors' => $validator->errors(),
            ]);
        }

        // ✅ STEP 1: Get or Create User
        $user = Auth::user();

        if (!$user) {
            // Check by phone
            $user = User::where('phone', $request->phone)->first();

            if (!$user) {
                // Generate a fallback email if not provided
                $phone_number = $request->phone;
                $email = !empty($request->email) ? $request->email : 'guest_' . $request->phone . '_' . uniqid() . '@example.com';

                $user = new User();
                $user->name = $request->name;
                $user->email = $email;
                $user->phone = $phone_number;
                // Guest accounts get a random, unguessable password. They cannot log in
                // with it; registering with the same phone claims the account instead.
                $user->password = Hash::make(Str::random(40));
                $user->save();
            }
        }

        // ✅ STEP 2: Save/Update Customer Address
        CustomerAddress::updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                // 'shipping' => $request->shipping_method,
            ],
        );

        // ✅ STEP 3: Calculate amounts (server-side; never trust client-supplied prices)
        $variant = ProductVariant::with('product')->find($request->product_id);

        if (!$variant) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found.',
            ]);
        }

        $quantity = max(1, (int) $request->selected_qty);
        $unitPrice = (float) $variant->selling_price;
        $subTotal = $unitPrice * $quantity;

        if ($variant->qty !== null && $variant->qty !== '' && (int) $variant->qty < $quantity) {
            return response()->json([
                'status' => false,
                'message' => 'Only ' . (int) $variant->qty . ' items in stock.',
            ]);
        }

        $freeDelivery = (bool) ($variant->product->free_delivery ?? false);
        $shipping = $freeDelivery ? 0 : ShippingCharge::rateForDistrict($request->district);
        $discount = 0;
        $couponCode = null;

        if (!empty($request->coupon)) {
            $coupon = DiscountCoupon::where('code', $request->coupon)
                ->where('status', 1)
                ->where('expires_at', '>=', now())
                ->where(function ($q) {
                    $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
                })
                ->first();

            if ($coupon && $subTotal >= (float) $coupon->min_amount) {
                $valid = true;

                if ($coupon->max_uses > 0 && $coupon->orders()->count() >= $coupon->max_uses) {
                    $valid = false;
                }

                if ($coupon->max_uses_user > 0 && $coupon->orders()->where('user_id', $user->id)->count() >= $coupon->max_uses_user) {
                    $valid = false;
                }

                if ($valid) {
                    $discount = $coupon->type === 'percent' ? ($subTotal * $coupon->discount_amount) / 100 : $coupon->discount_amount;

                    if ($discount > $subTotal) {
                        $discount = $subTotal;
                    }

                    $couponCode = $coupon->code;
                }
            }
        }

        $grandTotal = $subTotal + $shipping - $discount;

        try {
            DB::beginTransaction();

            // ✅ STEP 4: Create Order
            $order = new Order();
            $order->user_id = $user->id;
            $order->subtotal = $subTotal;
            $order->shipping = $shipping;
            $order->discount = $discount;
            $order->coupon_code = $couponCode;
            $order->grand_total = $grandTotal;
            $order->name = $request->name;
            $order->phone = $request->phone;
            $order->address = $request->address;
            $order->notes = $request->order_note;
            $order->save(); // save first to get $order->id

            // Now generate custom order_id like YYYYMMDD + id (e.g., 20251010023)
            $order->order_id = date('Y') . str_pad($order->id, 4, '0', STR_PAD_LEFT);
            $order->save();

            // ✅ STEP 5: Store Order Items

            $orderItem = new OrderItem();
            $orderItem->product_id = $variant->id;
            $orderItem->order_id = $order->id;
            $orderItem->name = $variant->sku;
            $orderItem->qty = $quantity;
            $orderItem->price = $unitPrice;
            // Inventory cost snapshot at order time (for accurate historical COGS).
            $orderItem->cost_price = $variant->average_cost ?? $variant->purchase_price ?? 0;
            $orderItem->total = $subTotal;
            $orderItem->free_delivery = $freeDelivery;
            $orderItem->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again.',
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Order saved successfully',
            'orderId' => $order->order_id,
        ]);
    }
}
