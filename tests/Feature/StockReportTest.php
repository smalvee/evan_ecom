<?php

namespace Tests\Feature;

use App\Models\NewProduct;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\User;
use App\Services\StockReportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StockReportTest extends TestCase
{
    use RefreshDatabase;

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

    protected function makeVariant(int $qty = 0, float $cost = 100): ProductVariant
    {
        $product = NewProduct::create([
            'name' => 'Air Fryer ' . uniqid(),
            'slug' => 'air-fryer-' . uniqid(),
            'sku' => 'PROD-' . uniqid(),
            'status' => 1,
        ]);

        return ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'AF-VAR-' . uniqid(),
            'is_variant' => 0,
            'qty' => $qty,
            'selling_price' => 200,
            'compare_price' => 220,
            'purchase_price' => $cost,
            'average_cost' => $cost,
            'allow_pre_order' => false,
        ]);
    }

    protected function makePurchase(ProductVariant $variant, int $qty, float $unitCost, string $date): void
    {
        $purchase = new Purchase();
        $purchase->supplier_id = 0;
        $purchase->date = $date;
        $purchase->total = $qty * $unitCost;
        $purchase->save();

        $item = new PurchaseItem();
        $item->purchase_id = $purchase->id;
        $item->date = $date;
        $item->variant_id = $variant->id;
        $item->p_name = $variant->product->name;
        $item->qty = $qty;
        $item->unit_cost = $unitCost;
        $item->selling_price = $unitCost;
        $item->save();

        $variant->qty = (int) $variant->qty + $qty;
        $variant->save();
    }

    protected function makeSale(User $customer, ProductVariant $variant, int $qty, string $date): void
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
        $order->status = 'shipped';
        $order->stock_deducted = true;
        $order->created_at = $date;
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
        $item->is_pre_order = false;
        $item->save();

        $variant->qty = max(0, (int) $variant->qty - $qty);
        $variant->save();
    }

    protected function stockFor(ProductVariant $variant, ?Carbon $from, ?Carbon $to): array
    {
        $rows = app(StockReportService::class)->rows($from, $to, ['search' => $variant->sku], 100);

        return collect($rows->items())->firstWhere('id', $variant->id)->stock;
    }

    /* ------------------------------------------------------------------ */
    /* Calculation                                                        */
    /* ------------------------------------------------------------------ */

    public function test_basic_movement_within_period(): void
    {
        $variant = $this->makeVariant(0, 100);
        $customer = $this->makeCustomer();

        $this->makePurchase($variant, 10, 100, '2026-01-05');
        $this->makeSale($customer, $variant, 3, '2026-01-10 12:00:00');

        $stock = $this->stockFor($variant, Carbon::parse('2026-01-01'), Carbon::parse('2026-01-31'));

        $this->assertSame(0.0, $stock['opening_qty']);
        $this->assertSame(0.0, $stock['opening_value']);

        $this->assertSame(10.0, $stock['purchase_qty']);
        $this->assertEqualsWithDelta(1000.0, $stock['purchase_value'], 0.001);
        $this->assertEqualsWithDelta(100.0, $stock['purchase_rate'], 0.001);

        $this->assertSame(3.0, $stock['sales_qty']);
        $this->assertEqualsWithDelta(300.0, $stock['sales_value'], 0.001);
        $this->assertEqualsWithDelta(100.0, $stock['sales_rate'], 0.001);

        $this->assertSame(7.0, $stock['closing_qty']);
        $this->assertEqualsWithDelta(700.0, $stock['closing_value'], 0.001);
        $this->assertEqualsWithDelta(100.0, $stock['closing_rate'], 0.001);
    }

    public function test_opening_stock_from_transactions_before_period(): void
    {
        $variant = $this->makeVariant(0, 100);

        $this->makePurchase($variant, 5, 100, '2025-12-20');

        $stock = $this->stockFor($variant, Carbon::parse('2026-01-01'), Carbon::parse('2026-01-31'));

        $this->assertSame(5.0, $stock['opening_qty']);
        $this->assertEqualsWithDelta(500.0, $stock['opening_value'], 0.001);

        $this->assertSame(0.0, $stock['purchase_qty']);
        $this->assertSame(0.0, $stock['sales_qty']);

        $this->assertSame(5.0, $stock['closing_qty']);
        $this->assertEqualsWithDelta(500.0, $stock['closing_value'], 0.001);
    }

    public function test_zero_stock_without_transactions_has_no_division_by_zero(): void
    {
        $variant = $this->makeVariant(0, 100);

        $stock = $this->stockFor($variant, Carbon::parse('2026-01-01'), Carbon::parse('2026-01-31'));

        $this->assertSame(0.0, $stock['opening_qty']);
        $this->assertSame(0.0, $stock['purchase_qty']);
        $this->assertSame(0.0, $stock['sales_qty']);
        $this->assertSame(0.0, $stock['closing_qty']);
        $this->assertSame(0.0, $stock['opening_rate']);
        $this->assertSame(0.0, $stock['closing_rate']);
    }

    public function test_purchase_return_reduces_stock(): void
    {
        $variant = $this->makeVariant(0, 100);

        $this->makePurchase($variant, 10, 100, '2026-01-05');

        $return = \App\Models\PurchaseReturn::create(['purchase_id' => Purchase::first()->id, 'return_amount' => 200]);
        \App\Models\PurchaseReturnItems::create([
            'purchase_return_id' => $return->id,
            'variant_id' => $variant->id,
            'qty' => 2,
            'unit_cost' => 100,
        ]);
        $return->created_at = '2026-01-08 10:00:00';
        $return->save();

        $variant->qty = 8;
        $variant->save();

        $stock = $this->stockFor($variant, Carbon::parse('2026-01-01'), Carbon::parse('2026-01-31'));

        $this->assertSame(8.0, $stock['purchase_qty']);
        $this->assertEqualsWithDelta(800.0, $stock['purchase_value'], 0.001);
        $this->assertSame(8.0, $stock['closing_qty']);
    }

    /* ------------------------------------------------------------------ */
    /* Route + authorization                                              */
    /* ------------------------------------------------------------------ */

    public function test_stock_report_page_renders_grouped_headers(): void
    {
        $admin = $this->makeAdmin();
        $this->makeVariant(0, 100);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.reports.stock'));

        $response->assertOk();
        $response->assertSee('Stock Report');
        $response->assertSee('Opening Stock');
        $response->assertSee('Closing Stock');
        $response->assertSee('SKU No');
        $response->assertSee('SKU Name');
    }

    public function test_guest_cannot_access_stock_report(): void
    {
        $this->get(route('admin.reports.stock'))->assertRedirect(route('admin.login'));
    }

    public function test_customer_cannot_access_stock_report(): void
    {
        $customer = $this->makeCustomer();

        $this->actingAs($customer)
            ->get(route('admin.reports.stock'))
            ->assertRedirect(route('admin.login'));
    }
}
