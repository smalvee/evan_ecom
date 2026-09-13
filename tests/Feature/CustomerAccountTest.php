<?php

namespace Tests\Feature;

use App\Models\NewProduct;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\User;
use App\Support\OrderStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerAccountTest extends TestCase
{
    use RefreshDatabase;

    /* ------------------------------------------------------------------ */
    /* Helpers                                                            */
    /* ------------------------------------------------------------------ */

    protected function makeCustomer(): User
    {
        $user = new User();
        $user->name = 'Customer ' . uniqid();
        $user->email = 'cust_' . uniqid() . '@example.com';
        $user->phone = '017' . random_int(10000000, 99999999);
        $user->password = Hash::make('secret');
        $user->role = 1;
        $user->save();

        return $user;
    }

    protected function makeOrder(User $customer, string $status = 'pending', float $total = 250): Order
    {
        $product = NewProduct::create([
            'name' => 'Product ' . uniqid(),
            'slug' => 'product-' . uniqid(),
            'sku' => 'PROD-' . uniqid(),
            'status' => 1,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'VAR-' . uniqid(),
            'is_variant' => 0,
            'qty' => 10,
            'selling_price' => $total,
            'compare_price' => $total + 20,
            'purchase_price' => $total / 2,
            'allow_pre_order' => false,
        ]);

        $order = new Order();
        $order->user_id = $customer->id;
        $order->subtotal = $total;
        $order->shipping = 0;
        $order->discount = 0;
        $order->grand_total = $total;
        $order->name = $customer->name;
        $order->phone = $customer->phone;
        $order->address = '123 Test Street';
        $order->notes = '';
        $order->status = $status;
        $order->stock_deducted = false;
        $order->save();
        $order->order_id = date('Y') . str_pad((string) $order->id, 4, '0', STR_PAD_LEFT);
        $order->save();

        $item = new OrderItem();
        $item->order_id = $order->id;
        $item->product_id = $variant->id;
        $item->name = $variant->sku;
        $item->qty = 1;
        $item->price = $total;
        $item->cost_price = $total / 2;
        $item->discount = 0;
        $item->total = $total;
        $item->free_delivery = false;
        $item->is_pre_order = false;
        $item->pre_order_status = null;
        $item->save();

        return $order;
    }

    /* ------------------------------------------------------------------ */
    /* Authentication                                                     */
    /* ------------------------------------------------------------------ */

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('account.userDashboard'))->assertRedirect(route('account.userLogin'));
        $this->get(route('account.orders'))->assertRedirect(route('account.userLogin'));
        $this->get(route('account.profile'))->assertRedirect(route('account.userLogin'));
    }

    /* ------------------------------------------------------------------ */
    /* Dashboard                                                          */
    /* ------------------------------------------------------------------ */

    public function test_dashboard_loads_and_shows_stats_and_recent_orders(): void
    {
        $customer = $this->makeCustomer();
        $this->makeOrder($customer, 'pending', 100);
        $this->makeOrder($customer, 'confirm', 200);
        $this->makeOrder($customer, 'shipped', 300);

        $response = $this->actingAs($customer)->get(route('account.userDashboard'));

        $response->assertOk();
        $response->assertSee($customer->name);
        $response->assertSee('Total Orders');
        $response->assertSee('Recent Orders');
        $response->assertSee('account-layout', false);
        $response->assertSee('account-sidebar', false);
        $response->assertSee('account-nav', false);
        $response->assertSee('account-stat-card', false);
        $response->assertSee('order-status--pending', false);
        $response->assertSee('order-status--confirmed', false);
        $response->assertSee('order-status--shipped', false);

        $this->assertSame(3, $response->viewData('stats')['total']);
        $this->assertSame(1, $response->viewData('stats')['pending']);
        $this->assertSame(1, $response->viewData('stats')['confirmed']);
        $this->assertSame(1, $response->viewData('stats')['shipped']);
    }

    public function test_dashboard_only_counts_own_orders(): void
    {
        $customer = $this->makeCustomer();
        $other = $this->makeCustomer();

        $this->makeOrder($customer, 'pending');
        $this->makeOrder($other, 'pending');
        $this->makeOrder($other, 'shipped');

        $response = $this->actingAs($customer)->get(route('account.userDashboard'));

        $this->assertSame(1, $response->viewData('stats')['total']);
        $this->assertCount(1, $response->viewData('recentOrders'));
    }

    public function test_dashboard_limits_recent_orders_to_five_latest(): void
    {
        $customer = $this->makeCustomer();
        foreach (range(1, 7) as $i) {
            $this->makeOrder($customer, 'pending', (float) $i);
        }

        $response = $this->actingAs($customer)->get(route('account.userDashboard'));

        $recent = $response->viewData('recentOrders');
        $this->assertCount(5, $recent);
        $this->assertSame(7.0, (float) $recent->first()->grand_total);
    }

    public function test_dashboard_empty_state_when_no_orders(): void
    {
        $customer = $this->makeCustomer();

        $response = $this->actingAs($customer)->get(route('account.userDashboard'));

        $response->assertOk();
        $response->assertSee('No orders yet');
        $response->assertSee('Start Shopping');
    }

    /* ------------------------------------------------------------------ */
    /* Order history                                                      */
    /* ------------------------------------------------------------------ */

    public function test_order_history_only_lists_own_orders_and_is_paginated(): void
    {
        $customer = $this->makeCustomer();
        $other = $this->makeCustomer();

        foreach (range(1, 12) as $i) {
            $this->makeOrder($customer, 'pending', (float) $i);
        }
        $this->makeOrder($other, 'shipped');

        $response = $this->actingAs($customer)->get(route('account.orders'));

        $response->assertOk();
        $orders = $response->viewData('orders');
        $this->assertSame(12, $orders->total());
        $this->assertCount(10, $orders->items());
    }

    public function test_order_history_orders_latest_first(): void
    {
        $customer = $this->makeCustomer();
        $first = $this->makeOrder($customer, 'pending');
        $second = $this->makeOrder($customer, 'pending');

        $response = $this->actingAs($customer)->get(route('account.orders'));

        $orders = $response->viewData('orders');
        $this->assertSame($second->id, $orders->first()->id);
    }

    public function test_order_history_status_filter(): void
    {
        $customer = $this->makeCustomer();
        $this->makeOrder($customer, 'pending');
        $this->makeOrder($customer, 'confirm');

        $response = $this->actingAs($customer)->get(route('account.orders', ['status' => 'confirm']));

        $orders = $response->viewData('orders');
        $this->assertSame(1, $orders->total());
        $this->assertSame('confirm', $orders->first()->status);
        $this->assertSame('confirm', $response->viewData('activeStatus'));
    }

    public function test_order_history_ignores_invalid_status_filter(): void
    {
        $customer = $this->makeCustomer();
        $this->makeOrder($customer, 'pending');

        $response = $this->actingAs($customer)->get(route('account.orders', ['status' => 'bogus']));

        $response->assertOk();
        $this->assertNull($response->viewData('activeStatus'));
        $this->assertSame(1, $response->viewData('orders')->total());
    }

    /* ------------------------------------------------------------------ */
    /* Order details + ownership (security)                               */
    /* ------------------------------------------------------------------ */

    public function test_owner_can_view_own_order(): void
    {
        $customer = $this->makeCustomer();
        $order = $this->makeOrder($customer, 'shipped', 999);

        $response = $this->actingAs($customer)->get(route('account.orderDetails', $order->id));

        $response->assertOk();
        $response->assertSee('#' . $order->order_id);
        $response->assertSee('order-status--shipped', false);
    }

    public function test_customer_cannot_view_another_customers_order(): void
    {
        $owner = $this->makeCustomer();
        $intruder = $this->makeCustomer();
        $order = $this->makeOrder($owner, 'pending');

        $this->actingAs($intruder)
            ->get(route('account.orderDetails', $order->id))
            ->assertNotFound();
    }

    public function test_missing_order_returns_404_not_500(): void
    {
        $customer = $this->makeCustomer();

        $this->actingAs($customer)
            ->get(route('account.orderDetails', 999999))
            ->assertNotFound();
    }

    public function test_guest_cannot_view_order_details(): void
    {
        $owner = $this->makeCustomer();
        $order = $this->makeOrder($owner, 'pending');

        $this->get(route('account.orderDetails', $order->id))
            ->assertRedirect(route('account.userLogin'));
    }

    /* ------------------------------------------------------------------ */
    /* Status mapping                                                     */
    /* ------------------------------------------------------------------ */

    public function test_status_mapping_hides_raw_database_values(): void
    {
        $this->assertSame('Pending', OrderStatus::label('pending'));
        $this->assertSame('Confirmed', OrderStatus::label('confirm'));
        $this->assertSame('Shipped', OrderStatus::label('shipped'));
        $this->assertSame('Cancelled', OrderStatus::label('cancell'));
        $this->assertSame('Unknown', OrderStatus::label('nonsense'));

        $this->assertSame('cancelled', OrderStatus::key('cancell'));
        $this->assertSame('unknown', OrderStatus::key(null));
    }

    public function test_cancelled_order_renders_cancelled_label(): void
    {
        $customer = $this->makeCustomer();
        $order = $this->makeOrder($customer, 'cancell');

        $response = $this->actingAs($customer)->get(route('account.orders'));

        $response->assertSee('order-status--cancelled', false);
        $response->assertSee('Cancelled');
    }

    /* ------------------------------------------------------------------ */
    /* Profile                                                            */
    /* ------------------------------------------------------------------ */

    public function test_profile_page_loads_with_account_layout(): void
    {
        $customer = $this->makeCustomer();

        $response = $this->actingAs($customer)->get(route('account.profile'));

        $response->assertOk();
        $response->assertSee('Personal Information');
        $response->assertSee('Update Password');
        $response->assertSee('account-nav-link', false);
        $response->assertSee('customer-account.css', false);
        // Existing profile AJAX wiring must survive the redesign.
        $response->assertSee('$("#profileupdate").submit', false);
        $response->assertSee('togglePassword', false);
    }

    public function test_order_details_renders_items_and_summary(): void
    {
        $customer = $this->makeCustomer();
        $order = $this->makeOrder($customer, 'confirm', 1234.5);

        $response = $this->actingAs($customer)->get(route('account.orderDetails', $order->id));

        $response->assertOk();
        $response->assertSee('order-item', false);
        $response->assertSee('Order Summary');
        $response->assertSee('Shipping Information');
        $response->assertSee('1,234.50');
        $response->assertSee('account-summary', false);
    }

    /* ------------------------------------------------------------------ */
    /* Navigation / active state                                          */
    /* ------------------------------------------------------------------ */

    public function test_header_shows_login_links_for_guest(): void
    {
        $response = $this->get(route('front.home'));

        $response->assertOk();
        $response->assertSee('My Account');
        $response->assertSee('Log In');
        $response->assertSee('Register');
    }

    public function test_header_shows_username_and_account_links_when_logged_in(): void
    {
        $customer = $this->makeCustomer();

        $response = $this->actingAs($customer)->get(route('front.home'));

        $response->assertOk();
        $response->assertSee($customer->name);
        $response->assertSee(route('account.userDashboard'), false);
        $response->assertSee(route('account.logout'), false);
    }

    public function test_sidebar_marks_current_page_active(): void
    {
        $customer = $this->makeCustomer();

        $orders = $this->actingAs($customer)->get(route('account.orders'));
        $orders->assertSee('account-nav-link active', false);

        $dashboard = $this->actingAs($customer)->get(route('account.userDashboard'));
        $dashboard->assertSee('account-nav-link active', false);
    }
}
