<?php

namespace Tests\Feature;

use App\Models\NewProduct;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\User;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PreOrderTest extends TestCase
{
    use RefreshDatabase;

    /* ------------------------------------------------------------------ */
    /* Helpers                                                            */
    /* ------------------------------------------------------------------ */

    protected function makeVariant(array $attributes = []): ProductVariant
    {
        $product = NewProduct::create([
            'name' => 'Test Product ' . uniqid(),
            'slug' => 'test-product-' . uniqid(),
            'sku' => 'PROD-' . uniqid(),
            'status' => 1,
        ]);

        return ProductVariant::create(array_merge([
            'product_id' => $product->id,
            'sku' => 'VAR-' . uniqid(),
            'is_variant' => 0,
            'qty' => 5,
            'selling_price' => 100,
            'compare_price' => 120,
            'purchase_price' => 60,
            'allow_pre_order' => false,
        ], $attributes));
    }

    protected function makeCustomer(): User
    {
        $user = new User();
        $user->name = 'Customer';
        $user->email = 'cust_' . uniqid() . '@example.com';
        $user->phone = '017' . random_int(10000000, 99999999);
        $user->password = Hash::make('secret');
        $user->role = 1;
        $user->save();

        return $user;
    }

    protected function makeAdmin(): User
    {
        $user = new User();
        $user->name = 'Admin';
        $user->email = 'admin_' . uniqid() . '@example.com';
        $user->phone = '018' . random_int(10000000, 99999999);
        $user->password = Hash::make('secret');
        $user->role = 2;
        $user->save();

        return $user;
    }

    protected function makeOrder(User $customer, ProductVariant $variant, int $qty, bool $preOrder, string $status = 'pending'): OrderItem
    {
        $order = new Order();
        $order->user_id = $customer->id;
        $order->subtotal = (float) $variant->selling_price * $qty;
        $order->shipping = 0;
        $order->discount = 0;
        $order->grand_total = $order->subtotal;
        $order->name = $customer->name;
        $order->phone = $customer->phone;
        $order->address = '123 Street';
        $order->notes = '';
        $order->status = $status;
        $order->stock_deducted = false;
        $order->save();
        $order->order_id = date('Y') . str_pad($order->id, 4, '0', STR_PAD_LEFT);
        $order->save();

        $item = new OrderItem();
        $item->order_id = $order->id;
        $item->product_id = $variant->id;
        $item->name = $variant->sku;
        $item->qty = $qty;
        $item->price = (float) $variant->selling_price;
        $item->cost_price = (float) $variant->purchase_price;
        $item->discount = 0;
        $item->total = (float) $variant->selling_price * $qty;
        $item->free_delivery = false;
        $item->is_pre_order = $preOrder;
        $item->pre_order_status = $preOrder ? 'pending' : null;
        $item->save();

        return $item;
    }

    protected function checkoutPayload(ProductVariant $variant, int $qty = 1, array $extra = []): array
    {
        return array_merge([
            'name' => 'Test Customer',
            'phone' => '01711111111',
            'address' => '123 Street',
            'district' => 'Dhaka',
            'product_id' => $variant->id,
            'selected_qty' => $qty,
        ], $extra);
    }

    /* ------------------------------------------------------------------ */
    /* 1-2: normal in-stock purchase                                      */
    /* ------------------------------------------------------------------ */

    public function test_in_stock_add_to_cart_succeeds(): void
    {
        $variant = $this->makeVariant(['qty' => 5]);

        $this->post(route('front.addToCart'), ['id' => $variant->id, 'qty' => 2])
            ->assertOk()
            ->assertJson(['status' => true]);
    }

    public function test_in_stock_single_checkout_succeeds(): void
    {
        $variant = $this->makeVariant(['qty' => 5]);

        $this->post(route('front.singCheckout', $variant->id), $this->checkoutPayload($variant, 2))
            ->assertOk()
            ->assertJson(['status' => true]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $variant->id,
            'qty' => 2,
            'is_pre_order' => false,
        ]);
    }

    /* ------------------------------------------------------------------ */
    /* 3-5: out of stock, pre-order disabled                              */
    /* ------------------------------------------------------------------ */

    public function test_out_of_stock_without_preorder_add_to_cart_rejected(): void
    {
        $variant = $this->makeVariant(['qty' => 0, 'allow_pre_order' => false]);

        $this->post(route('front.addToCart'), ['id' => $variant->id, 'qty' => 1])
            ->assertOk()
            ->assertJson(['status' => false]);
    }

    public function test_out_of_stock_without_preorder_checkout_rejected(): void
    {
        $variant = $this->makeVariant(['qty' => 0, 'allow_pre_order' => false]);

        $this->post(route('front.singCheckout', $variant->id), $this->checkoutPayload($variant, 1))
            ->assertOk()
            ->assertJson(['status' => false]);

        $this->assertDatabaseCount('order_items', 0);
    }

    public function test_out_of_stock_without_preorder_product_page_shows_stock_out(): void
    {
        $variant = $this->makeVariant(['qty' => 0, 'allow_pre_order' => false]);
        $product = $variant->product;

        $this->get(route('Product_details.home', $product->slug))
            ->assertOk()
            ->assertSee('Stock Out')
            ->assertSee('id="add-to-cart-btn"', false)
            ->assertSee('id="buy-now-btn"', false)
            // The embedded variant state tells the UI this is a stock-out (no pre-order).
            ->assertSee('"allow_pre_order":false', false)
            ->assertSee('"stock":0', false);
    }

    /* ------------------------------------------------------------------ */
    /* 6-8: out of stock, pre-order enabled                               */
    /* ------------------------------------------------------------------ */

    public function test_out_of_stock_with_preorder_product_page_shows_notice(): void
    {
        $variant = $this->makeVariant(['qty' => 0, 'allow_pre_order' => true]);
        $product = $variant->product;

        $this->get(route('Product_details.home', $product->slug))
            ->assertOk()
            ->assertSee('available for pre-order')
            ->assertSee('id="pre-order-btn"', false)
            ->assertSee('Pre Order')
            ->assertSee('pd-actions', false)
            // The embedded variant state enables the pre-order UI.
            ->assertSee('"allow_pre_order":true', false)
            ->assertSee('"stock":0', false);
    }

    public function test_out_of_stock_with_preorder_checkout_creates_preorder_and_does_not_deduct_stock(): void
    {
        $variant = $this->makeVariant(['qty' => 0, 'allow_pre_order' => true]);

        $this->post(route('front.singCheckout', $variant->id), $this->checkoutPayload($variant, 3))
            ->assertOk()
            ->assertJson(['status' => true]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $variant->id,
            'qty' => 3,
            'is_pre_order' => true,
            'pre_order_status' => 'pending',
        ]);

        // Stock must never become negative and must not be deducted at order time.
        $this->assertSame(0, (int) $variant->fresh()->qty);
    }

    public function test_preorder_does_not_make_stock_negative(): void
    {
        $variant = $this->makeVariant(['qty' => 0, 'allow_pre_order' => true]);

        $this->post(route('front.singCheckout', $variant->id), $this->checkoutPayload($variant, 10))
            ->assertJson(['status' => true]);

        $this->assertGreaterThanOrEqual(0, (int) $variant->fresh()->qty);
    }

    /* ------------------------------------------------------------------ */
    /* 12-14: server-side trust / tampering                               */
    /* ------------------------------------------------------------------ */

    public function test_customer_cannot_fake_allow_pre_order(): void
    {
        $variant = $this->makeVariant(['qty' => 0, 'allow_pre_order' => false]);

        // Client claims pre-order is allowed; server must reject.
        $this->post(route('front.singCheckout', $variant->id), $this->checkoutPayload($variant, 1, [
            'allow_pre_order' => 1,
            'is_pre_order' => 1,
        ]))->assertJson(['status' => false]);

        $this->assertDatabaseCount('order_items', 0);
    }

    public function test_customer_cannot_fake_is_pre_order_for_in_stock_item(): void
    {
        $variant = $this->makeVariant(['qty' => 5, 'allow_pre_order' => true]);

        // Item is in stock, so it must be a normal order even if the client claims otherwise.
        $this->post(route('front.singCheckout', $variant->id), $this->checkoutPayload($variant, 1, [
            'is_pre_order' => 1,
        ]))->assertJson(['status' => true]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $variant->id,
            'is_pre_order' => false,
        ]);
    }

    public function test_preorder_price_is_taken_from_database(): void
    {
        $variant = $this->makeVariant(['qty' => 0, 'allow_pre_order' => true, 'selling_price' => 250]);

        $this->post(route('front.singCheckout', $variant->id), $this->checkoutPayload($variant, 2, [
            'price' => 1,
            'selling_price' => 1,
            'subtotal' => 1,
            'grand_total' => 1,
        ]))->assertJson(['status' => true]);

        $item = OrderItem::where('product_id', $variant->id)->first();
        $this->assertNotNull($item);
        $this->assertEqualsWithDelta(250.0, (float) $item->price, 0.001);
        $this->assertEqualsWithDelta(500.0, (float) $item->total, 0.001);
    }

    /* ------------------------------------------------------------------ */
    /* 15: mixed cart                                                     */
    /* ------------------------------------------------------------------ */

    public function test_mixed_cart_handles_normal_and_preorder_items(): void
    {
        $normal = $this->makeVariant(['qty' => 10, 'allow_pre_order' => false]);
        $pre = $this->makeVariant(['qty' => 0, 'allow_pre_order' => true]);

        $this->startSession();
        Cart::destroy();
        Cart::add($normal->id, $normal->sku, 1, (float) $normal->selling_price, []);
        Cart::add($pre->id, $pre->sku, 2, (float) $pre->selling_price, []);

        $this->post(route('front.checkout'), [
            'name' => 'Test Customer',
            'phone' => '01711111111',
            'address' => '123 Street',
            'district' => 'Dhaka',
        ])->assertJson(['status' => true]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $normal->id,
            'is_pre_order' => false,
        ]);
        $this->assertDatabaseHas('order_items', [
            'product_id' => $pre->id,
            'qty' => 2,
            'is_pre_order' => true,
        ]);
    }

    /* ------------------------------------------------------------------ */
    /* 16-17: admin visibility                                            */
    /* ------------------------------------------------------------------ */

    public function test_admin_can_see_preorders(): void
    {
        $admin = $this->makeAdmin();
        $customer = $this->makeCustomer();
        $variant = $this->makeVariant(['qty' => 0, 'allow_pre_order' => true]);
        $item = $this->makeOrder($customer, $variant, 2, true);
        $orderCode = $item->order->order_id;

        $this->actingAs($admin, 'admin')
            ->get(route('admin.pre_orders.index'))
            ->assertOk()
            ->assertSee($orderCode)
            ->assertSee($variant->sku);
    }

    public function test_admin_preorder_list_shows_stock_available(): void
    {
        $admin = $this->makeAdmin();
        $customer = $this->makeCustomer();
        $variant = $this->makeVariant(['qty' => 5, 'allow_pre_order' => true]);
        $this->makeOrder($customer, $variant, 2, true);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.pre_orders.index'))
            ->assertOk()
            ->assertSee('Stock Available');
    }

    /* ------------------------------------------------------------------ */
    /* 18-22: admin processing                                            */
    /* ------------------------------------------------------------------ */

    public function test_admin_cannot_process_preorder_with_insufficient_stock(): void
    {
        $admin = $this->makeAdmin();
        $customer = $this->makeCustomer();
        $variant = $this->makeVariant(['qty' => 2, 'allow_pre_order' => true]);
        $item = $this->makeOrder($customer, $variant, 5, true);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.pre_orders.process', $item->id))
            ->assertOk()
            ->assertJson(['status' => false]);

        $this->assertSame(2, (int) $variant->fresh()->qty);
        $this->assertSame('pending', $item->fresh()->pre_order_status);
    }

    public function test_admin_can_process_preorder_when_stock_available(): void
    {
        $admin = $this->makeAdmin();
        $customer = $this->makeCustomer();
        $variant = $this->makeVariant(['qty' => 5, 'allow_pre_order' => true]);
        $item = $this->makeOrder($customer, $variant, 3, true);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.pre_orders.process', $item->id))
            ->assertOk()
            ->assertJson(['status' => true]);

        // 5 - 3 = 2, deducted exactly once.
        $this->assertSame(2, (int) $variant->fresh()->qty);
        $this->assertSame('processing', $item->fresh()->pre_order_status);
    }

    public function test_processing_preorder_deducts_stock_once(): void
    {
        $admin = $this->makeAdmin();
        $customer = $this->makeCustomer();
        $variant = $this->makeVariant(['qty' => 5, 'allow_pre_order' => true]);
        $item = $this->makeOrder($customer, $variant, 3, true);

        // Two processing attempts (simulates a double-click / concurrent admin).
        $this->actingAs($admin, 'admin')->post(route('admin.pre_orders.process', $item->id))->assertJson(['status' => true]);
        $this->actingAs($admin, 'admin')->post(route('admin.pre_orders.process', $item->id))->assertJson(['status' => true]);

        // Stock deducted exactly once: 5 - 3 = 2.
        $this->assertSame(2, (int) $variant->fresh()->qty);
    }

    public function test_processed_preorder_moves_into_normal_workflow(): void
    {
        $admin = $this->makeAdmin();
        $customer = $this->makeCustomer();
        $variant = $this->makeVariant(['qty' => 5, 'allow_pre_order' => true]);
        $item = $this->makeOrder($customer, $variant, 2, true);
        $orderId = $item->order_id;

        $this->actingAs($admin, 'admin')->post(route('admin.pre_orders.process', $item->id))->assertJson(['status' => true]);

        $order = Order::find($orderId);
        $this->assertSame('confirm', $order->status);
        $this->assertTrue((bool) $order->stock_deducted);

        // Historical pre-order flag is preserved.
        $this->assertTrue((bool) $item->fresh()->is_pre_order);
    }

    /* ------------------------------------------------------------------ */
    /* Admin order detail renders the compact order-items table           */
    /* ------------------------------------------------------------------ */

    public function test_admin_order_details_page_renders_order_items_table(): void
    {
        $admin = $this->makeAdmin();
        $customer = $this->makeCustomer();
        $variant = $this->makeVariant(['qty' => 5]);
        $item = $this->makeOrder($customer, $variant, 2, false);

        $this->actingAs($admin, 'admin')
            ->get(route('orders.details', $item->order_id))
            ->assertOk()
            ->assertSee('Order Status')
            ->assertSee('Customer Note')
            ->assertSee('Internal Order Note')
            ->assertSee('Order Items')
            ->assertSee('btn-cancel-item', false);
    }

    /* ------------------------------------------------------------------ */
    /* orders.update_full contract (used by the redesigned page)          */
    /* ------------------------------------------------------------------ */

    public function test_admin_can_update_order_through_update_full_endpoint(): void
    {
        $admin = $this->makeAdmin();
        $customer = $this->makeCustomer();
        $variant = $this->makeVariant(['qty' => 10, 'selling_price' => 100]);
        $item = $this->makeOrder($customer, $variant, 2, false);
        $orderId = $item->order_id;

        $this->actingAs($admin, 'admin')
            ->post(route('orders.update_full', $orderId), [
                'status' => 'confirm',
                'f_name' => 'Updated Customer',
                'address' => 'Updated Address',
                'admin_note' => 'Internal note',
                'item_qty' => [$item->id => 3],
                'item_discount' => [$item->id => 10],
            ])
            ->assertOk()
            ->assertJson(['status' => true]);

        $order = Order::find($orderId);
        $this->assertSame('confirm', $order->status);
        $this->assertSame('Updated Customer', $order->name);
        $this->assertSame('Updated Address', $order->address);
        $this->assertSame('Internal note', $order->admin_note);
        $this->assertTrue((bool) $order->stock_deducted);

        // Line total is recalculated server-side from the stored price (100*3 - 10).
        $this->assertEqualsWithDelta(290.0, (float) $item->fresh()->total, 0.001);
        $this->assertEqualsWithDelta(290.0, (float) $order->subtotal, 0.001);

        // Stock deducted exactly once: 10 - 3 = 7.
        $this->assertSame(7, (int) $variant->fresh()->qty);
    }

    public function test_admin_can_add_and_cancel_items_through_update_full(): void
    {
        $admin = $this->makeAdmin();
        $customer = $this->makeCustomer();
        $variant = $this->makeVariant(['qty' => 10, 'selling_price' => 100]);
        $extra = $this->makeVariant(['qty' => 10, 'selling_price' => 50]);
        $item = $this->makeOrder($customer, $variant, 2, false);
        $orderId = $item->order_id;

        $this->actingAs($admin, 'admin')
            ->post(route('orders.update_full', $orderId), [
                'status' => 'pending',
                'f_name' => $customer->name,
                'address' => 'Address',
                'admin_note' => '',
                'cancel_items' => [$item->id],
                'new_variant_id' => [$extra->id],
                'new_qty' => [2],
                'new_discount' => [5],
            ])
            ->assertOk()
            ->assertJson(['status' => true]);

        // Original item cancelled (removed).
        $this->assertNull(OrderItem::find($item->id));

        // New item added with the server-side variant price (50*2 - 5 = 95).
        $newItem = OrderItem::where('order_id', $orderId)->first();
        $this->assertNotNull($newItem);
        $this->assertSame($extra->id, (int) $newItem->product_id);
        $this->assertEqualsWithDelta(50.0, (float) $newItem->price, 0.001);
        $this->assertEqualsWithDelta(95.0, (float) $newItem->total, 0.001);
    }

    /* ------------------------------------------------------------------ */
    /* Admin create order: free delivery waives the delivery charge        */
    /* ------------------------------------------------------------------ */

    public function test_admin_create_order_waives_shipping_for_free_delivery_items(): void
    {
        $admin = $this->makeAdmin();
        $variant = $this->makeVariant(['qty' => 10, 'selling_price' => 100]);
        $variant->product->update(['free_delivery' => true]);

        $this->actingAs($admin, 'admin')
            ->post(route('orders.store'), [
                'name' => 'Customer',
                'phone' => '01711111111',
                'address' => 'Addr',
                'shipping_method' => '100',
                'shipping_amount' => 100,
                'products' => [
                    ['id' => $variant->id, 'qty' => 2, 'discount' => 0],
                ],
            ])
            ->assertOk()
            ->assertJson(['status' => true]);

        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertEqualsWithDelta(0.0, (float) $order->shipping, 0.001);
        $this->assertEqualsWithDelta(200.0, (float) $order->grand_total, 0.001);
    }

    public function test_admin_create_order_charges_shipping_for_non_free_delivery_items(): void
    {
        $admin = $this->makeAdmin();
        $variant = $this->makeVariant(['qty' => 10, 'selling_price' => 100]);

        $this->actingAs($admin, 'admin')
            ->post(route('orders.store'), [
                'name' => 'Customer',
                'phone' => '01711111112',
                'address' => 'Addr',
                'shipping_method' => '100',
                'shipping_amount' => 100,
                'products' => [
                    ['id' => $variant->id, 'qty' => 2, 'discount' => 0],
                ],
            ])
            ->assertOk()
            ->assertJson(['status' => true]);

        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertEqualsWithDelta(100.0, (float) $order->shipping, 0.001);
        $this->assertEqualsWithDelta(300.0, (float) $order->grand_total, 0.001);
    }

    public function test_admin_create_order_page_renders_searchable_selects(): void
    {
        $admin = $this->makeAdmin();
        $this->makeVariant(['qty' => 5]);

        $this->actingAs($admin, 'admin')
            ->get(route('orders.create'))
            ->assertOk()
            ->assertSee('id="productSearch"', false)
            ->assertSee('id="productRows"', false)
            ->assertSee('id="summaryItemCount"', false)
            ->assertSee('js-select2', false)
            ->assertSee('ProductAutocomplete', false)
            ->assertSee('id="shipping_method"', false)
            ->assertSee('name="sum_subTotal"', false)
            ->assertSee('name="shipping_amount"', false)
            ->assertSee('name="discount_amount"', false)
            ->assertSee('name="total_amount"', false);
    }

    public function test_admin_create_purchase_page_renders(): void
    {
        $admin = $this->makeAdmin();
        $this->makeVariant(['qty' => 5]);

        $this->actingAs($admin, 'admin')
            ->get(route('purchase.create'))
            ->assertOk()
            ->assertSee('Purchase Information')
            ->assertSee('id="supplier_id"', false)
            ->assertSee('id="purchaseRows"', false)
            ->assertSee('id="addProductBtn"', false)
            ->assertSee('purchase-product-input', false)
            ->assertSee('ProductAutocomplete', false)
            ->assertSee('name="total_purchase"', false)
            ->assertSee('Save Purchase');
    }

    public function test_admin_can_create_purchase_through_store_endpoint(): void
    {
        $admin = $this->makeAdmin();
        $variant = $this->makeVariant(['qty' => 0, 'selling_price' => 100, 'purchase_price' => 60]);

        $supplier = new \App\Models\Supplier();
        $supplier->name = 'Test Supplier';
        $supplier->email = 'supplier_' . uniqid() . '@example.com';
        $supplier->phone = '01700000000';
        $supplier->address = 'Supplier Address';
        $supplier->save();

        $this->actingAs($admin, 'admin')
            ->post(route('purchase.store'), [
                'supplier_id' => $supplier->id,
                'date' => now()->toDateString(),
                'p_name' => [$variant->product->name],
                'variant_id' => [$variant->id],
                'qty' => [5],
                'unit_cost' => [60],
                'profit_amount' => [20],
                'discount' => [5],
            ])
            ->assertOk()
            ->assertJson(['status' => true]);

        $purchase = \App\Models\Purchase::first();
        $this->assertNotNull($purchase);
        $this->assertSame($supplier->id, (int) $purchase->supplier_id);
        $this->assertEqualsWithDelta(300.0, (float) $purchase->total, 0.001);

        // Stock increased by the purchased qty (existing recordPurchase logic).
        $this->assertSame(5, (int) $variant->fresh()->qty);

        $this->assertDatabaseHas('purchase_items', [
            'variant_id' => $variant->id,
            'qty' => 5,
        ]);
    }
}
