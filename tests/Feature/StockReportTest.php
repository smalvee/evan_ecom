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
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelType;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
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
        $to = $to?->copy()->endOfDay();

        $rows = app(StockReportService::class)->rows($from, $to, ['search' => $variant->sku], 100);

        return collect($rows->items())->firstWhere('id', $variant->id)->stock;
    }

    protected function makeXlsxSheet(array $items, array $meta): \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
    {
        $raw = Excel::raw(new \App\Exports\StockReportExport($items, $meta), ExcelType::XLSX);

        $tmp = sys_get_temp_dir() . '/stock_' . uniqid() . '.xlsx';
        file_put_contents($tmp, $raw);

        $sheet = IOFactory::load($tmp)->getActiveSheet();
        unlink($tmp);

        return $sheet;
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

    /* ------------------------------------------------------------------ */
    /* Weighted average cost                                              */
    /* ------------------------------------------------------------------ */

    public function test_weighted_average_cost_after_purchases_at_different_rates(): void
    {
        $variant = $this->makeVariant(0, 100);
        $customer = $this->makeCustomer();

        $this->makePurchase($variant, 10, 100, '2026-01-05');
        $this->makePurchase($variant, 10, 200, '2026-01-10');
        $this->makeSale($customer, $variant, 5, '2026-01-15 12:00:00');

        $stock = $this->stockFor($variant, Carbon::parse('2026-01-01'), Carbon::parse('2026-01-31'));

        $this->assertSame(20.0, $stock['purchase_qty']);
        $this->assertEqualsWithDelta(3000.0, $stock['purchase_value'], 0.001);
        $this->assertEqualsWithDelta(150.0, $stock['purchase_rate'], 0.001);

        $this->assertSame(5.0, $stock['sales_qty']);
        $this->assertEqualsWithDelta(750.0, $stock['sales_value'], 0.001);
        $this->assertEqualsWithDelta(150.0, $stock['sales_rate'], 0.001);

        $this->assertSame(15.0, $stock['closing_qty']);
        $this->assertEqualsWithDelta(2250.0, $stock['closing_value'], 0.001);
        $this->assertEqualsWithDelta(150.0, $stock['closing_rate'], 0.001);
    }

    public function test_sale_uses_average_cost_at_time_of_sale(): void
    {
        $variant = $this->makeVariant(0, 100);
        $customer = $this->makeCustomer();

        $this->makePurchase($variant, 10, 100, '2026-01-05');
        $this->makeSale($customer, $variant, 5, '2026-01-10 12:00:00'); // avg 100 -> COGS 500
        $this->makePurchase($variant, 10, 200, '2026-01-15'); // avg -> (500 + 2000)/15 = 166.67
        $this->makeSale($customer, $variant, 5, '2026-01-20 12:00:00'); // COGS 833.33

        $stock = $this->stockFor($variant, Carbon::parse('2026-01-01'), Carbon::parse('2026-01-31'));

        // 500 + 833.33 = 1333.33 total cost of goods sold.
        $this->assertSame(10.0, $stock['sales_qty']);
        $this->assertEqualsWithDelta(1333.33, $stock['sales_value'], 0.01);

        $this->assertSame(10.0, $stock['closing_qty']);
        $this->assertEqualsWithDelta(1666.67, $stock['closing_value'], 0.01);
    }

    public function test_positive_and_negative_adjustments(): void
    {
        $variant = $this->makeVariant(0, 100);
        $this->makePurchase($variant, 10, 100, '2026-01-05');

        $increase = new \App\Models\ProductAdjustment();
        $increase->product_id = $variant->product_id;
        $increase->variant_id = $variant->id;
        $increase->adjustment_type = 'increase';
        $increase->quantity = 5;
        $increase->stock_before = 10;
        $increase->stock_after = 15;
        $increase->created_at = '2026-01-08 10:00:00';
        $increase->save();

        $decrease = new \App\Models\ProductAdjustment();
        $decrease->product_id = $variant->product_id;
        $decrease->variant_id = $variant->id;
        $decrease->adjustment_type = 'decrease';
        $decrease->quantity = -3;
        $decrease->stock_before = 15;
        $decrease->stock_after = 12;
        $decrease->created_at = '2026-01-12 10:00:00';
        $decrease->save();

        $variant->qty = 12;
        $variant->save();

        $stock = $this->stockFor($variant, Carbon::parse('2026-01-01'), Carbon::parse('2026-01-31'));

        $this->assertSame(15.0, $stock['purchase_qty']);
        $this->assertEqualsWithDelta(1500.0, $stock['purchase_value'], 0.001);

        $this->assertSame(3.0, $stock['sales_qty']);
        $this->assertEqualsWithDelta(300.0, $stock['sales_value'], 0.001);

        $this->assertSame(12.0, $stock['closing_qty']);
        $this->assertEqualsWithDelta(1200.0, $stock['closing_value'], 0.001);
    }

    public function test_legacy_opening_balance_is_reconciled_without_double_counting(): void
    {
        $variant = $this->makeVariant(10, 100);

        $stock = $this->stockFor($variant, Carbon::parse('2026-01-01'), Carbon::parse('2026-01-31'));

        $this->assertSame(10.0, $stock['opening_qty']);
        $this->assertEqualsWithDelta(1000.0, $stock['opening_value'], 0.001);
        $this->assertSame(0.0, $stock['purchase_qty']);
        $this->assertSame(10.0, $stock['closing_qty']);
        $this->assertEqualsWithDelta(1000.0, $stock['closing_value'], 0.001);
    }

    public function test_transactions_on_boundary_dates_are_included(): void
    {
        $variant = $this->makeVariant(0, 100);
        $customer = $this->makeCustomer();

        $this->makePurchase($variant, 5, 100, '2026-01-01');
        $this->makeSale($customer, $variant, 2, '2026-01-31 23:59:59');

        $stock = $this->stockFor($variant, Carbon::parse('2026-01-01'), Carbon::parse('2026-01-31'));

        $this->assertSame(0.0, $stock['opening_qty']);
        $this->assertSame(5.0, $stock['purchase_qty']);
        $this->assertSame(2.0, $stock['sales_qty']);
        $this->assertSame(3.0, $stock['closing_qty']);
    }

    public function test_fractional_unit_cost_is_preserved(): void
    {
        $variant = $this->makeVariant(0, 100);

        $this->makePurchase($variant, 10, 100.5, '2026-01-05');

        $stock = $this->stockFor($variant, Carbon::parse('2026-01-01'), Carbon::parse('2026-01-31'));

        $this->assertEqualsWithDelta(1005.0, $stock['purchase_value'], 0.001);
        $this->assertEqualsWithDelta(100.5, $stock['purchase_rate'], 0.001);
        $this->assertEqualsWithDelta(1005.0, $stock['closing_value'], 0.001);
    }

    /* ------------------------------------------------------------------ */
    /* Filters                                                            */
    /* ------------------------------------------------------------------ */

    public function test_search_filter(): void
    {
        $this->makeVariant(0, 100);
        $variant = $this->makeVariant(0, 100);

        $rows = app(StockReportService::class)->rows(null, null, ['search' => $variant->sku], 100);

        $this->assertSame(1, $rows->total());
        $this->assertSame($variant->id, (int) $rows->items()[0]->id);
    }

    /* ------------------------------------------------------------------ */
    /* Export consistency                                                 */
    /* ------------------------------------------------------------------ */

    public function test_xlsx_export_has_grouped_headers_and_merges(): void
    {
        $this->makeVariant(0, 100);

        $rows = app(StockReportService::class)->rows(null, null, [], 100);
        $sheet = $this->makeXlsxSheet($rows->items(), [
            'range_label' => 'All Time', 'category' => null, 'brand' => null, 'search' => null, 'generated_at' => 'x',
        ]);

        $this->assertSame('Stock Report', $sheet->getTitle());
        $this->assertSame('Stock Report', $sheet->getCell('A1')->getValue());
        $this->assertSame('SKU No', $sheet->getCell('A5')->getValue());
        $this->assertSame('Opening Stock', $sheet->getCell('C5')->getValue());
        $this->assertSame('Purchase', $sheet->getCell('F5')->getValue());
        $this->assertSame('SKU No', $sheet->getCell('A6')->getValue());
        $this->assertSame('SKU Name', $sheet->getCell('B6')->getValue());
        $this->assertSame('Opening Qty', $sheet->getCell('C6')->getValue());

        $merges = $sheet->getMergeCells();
        $this->assertContains('C5:E5', $merges);
        $this->assertContains('L5:N5', $merges);
        $this->assertNotContains('A5:A6', $merges);
        $this->assertNotContains('B5:B6', $merges);
    }

    public function test_xlsx_export_cells_are_numeric_with_formats(): void
    {
        $this->makeVariant(10, 100);

        $rows = app(StockReportService::class)->rows(null, null, [], 100);
        $sheet = $this->makeXlsxSheet($rows->items(), [
            'range_label' => 'All Time', 'category' => null, 'brand' => null, 'search' => null, 'generated_at' => 'x',
        ]);

        // First data row is row 7.
        $this->assertSame(DataType::TYPE_NUMERIC, $sheet->getCell('C7')->getDataType());
        $this->assertSame(10.0, $sheet->getCell('C7')->getValue());

        // Number formats.
        $this->assertStringContainsString('#,##0', $sheet->getStyle('C7')->getNumberFormat()->getFormatCode());
        $this->assertStringContainsString('#,##0', $sheet->getStyle('E7')->getNumberFormat()->getFormatCode());
    }

    public function test_xlsx_export_matches_web_report_data(): void
    {
        $variant = $this->makeVariant(0, 100);
        $this->makePurchase($variant, 10, 100, '2026-01-05');

        $from = Carbon::parse('2026-01-01');
        $to = Carbon::parse('2026-01-31')->endOfDay();

        $stock = $this->stockFor($variant, $from, $to);

        $rows = app(StockReportService::class)->rows($from, $to, [], 100);
        $sheet = $this->makeXlsxSheet($rows->items(), [
            'range_label' => 'All Time', 'category' => null, 'brand' => null, 'search' => null, 'generated_at' => 'x',
        ]);

        // First data row (row 7): sku, name, opening qty/rate/value, purchase qty...
        $this->assertSame($variant->sku, $sheet->getCell('A7')->getValue());
        $this->assertEqualsWithDelta($stock['purchase_qty'], $sheet->getCell('F7')->getValue(), 0.001);
        $this->assertEqualsWithDelta($stock['closing_value'], $sheet->getCell('N7')->getValue(), 0.001);
    }

    public function test_formula_injection_is_sanitized(): void
    {
        $variant = $this->makeVariant(0, 100);
        $variant->product->update(['name' => '=1+1']);

        $rows = app(StockReportService::class)->rows(null, null, [], 100);
        $sheet = $this->makeXlsxSheet($rows->items(), [
            'range_label' => 'All Time', 'category' => null, 'brand' => null, 'search' => null, 'generated_at' => 'x',
        ]);

        $this->assertSame("'=1+1", $sheet->getCell('B7')->getValue());
    }

    public function test_export_endpoint_requires_admin(): void
    {
        $this->get(route('admin.reports.export', ['report' => 'stock']))
            ->assertRedirect(route('admin.login'));
    }
}
