<?php

namespace App\Http\Controllers\admin;

use App\Exports\ReportExport;
use App\Exports\SalesReportExport;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Supplier;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    protected ReportService $reports;

    public function __construct(ReportService $reports)
    {
        $this->reports = $reports;
    }

    /* =====================================================================
       SHARED HELPERS
       ===================================================================== */

    protected function filterOptions(): array
    {
        return [
            'statuses' => $this->reports->statusOptions(),
            'payments' => $this->reports->paymentOptions(),
            'ranges' => $this->reports->rangeOptions(),
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'brands' => Brand::orderBy('name')->get(['id', 'name']),
            'suppliers' => Supplier::orderBy('name')->get(['id', 'name']),
        ];
    }

    /* =====================================================================
       REPORTS DASHBOARD
       ===================================================================== */

    public function dashboard(Request $request)
    {
        [$from, $to, $range] = $this->reports->resolveRange($request);
        $data = $this->reports->dashboard($from, $to);

        $group = ($from && $to && $from->diffInDays($to) > 92) ? 'month' : 'day';
        $trend = $this->reports->salesTrend($from, $to, null, $group);

        $growth = [
            'revenue' => $data['prev_sales'] ? $this->reports->growth($data['sales']['net'], $data['prev_sales']['net']) : null,
            'orders' => $data['prev_sales'] ? $this->reports->growth($data['sales']['orders'], $data['prev_sales']['orders']) : null,
            'profit' => $data['prev_sales'] ? $this->reports->growth($data['sales']['gross_profit'], $data['prev_sales']['gross_profit']) : null,
        ];

        return view('admin.reports.dashboard', array_merge($data, $this->filterOptions(), [
            'from' => $from,
            'to' => $to,
            'range' => $range,
            'rangeLabel' => $this->reports->rangeLabel($from, $to, $range),
            'growth' => $growth,
            'trend' => $trend,
            'trendGroup' => $group,
        ]));
    }

    /* =====================================================================
       SALES REPORT
       ===================================================================== */

    public function sales_index(Request $request)
    {
        [$from, $to, $range] = $this->reports->resolveRange($request);
        $status = $request->filled('status') ? $request->status : null;
        $payment = $request->filled('payment') ? $request->payment : null;
        $group = in_array($request->get('group'), ['day', 'week', 'month']) ? $request->get('group') : 'day';

        $summary = $this->reports->salesSummary($from, $to, $status, $payment);
        $trend = $this->reports->salesTrend($from, $to, $status, $group);
        $rows = $this->reports->salesRows($from, $to, $status, 'day');

        $prev = $this->reports->previousPeriod($from, $to);
        $prevSummary = ($prev[0] && $prev[1]) ? $this->reports->salesSummary($prev[0], $prev[1], $status, $payment) : null;

        $growth = $prevSummary ? [
            'gross' => $this->reports->growth($summary['gross'], $prevSummary['gross']),
            'net' => $this->reports->growth($summary['net'], $prevSummary['net']),
            'orders' => $this->reports->growth($summary['orders'], $prevSummary['orders']),
        ] : ['gross' => null, 'net' => null, 'orders' => null];

        return view('admin.report.new_sales', array_merge($this->filterOptions(), [
            'from' => $from,
            'to' => $to,
            'range' => $range,
            'rangeLabel' => $this->reports->rangeLabel($from, $to, $range),
            'group' => $group,
            'summary' => $summary,
            'trend' => $trend,
            'rows' => $rows,
            'prevSummary' => $prevSummary,
            'growth' => $growth,
        ]));
    }

    /* =====================================================================
       PRODUCT SALES REPORT (item-wise)
       ===================================================================== */

    public function item_sales_index(Request $request)
    {
        [$from, $to, $range] = $this->reports->resolveRange($request);

        $filters = [
            'status' => $request->filled('status') ? $request->status : null,
            'category' => $request->filled('category') ? $request->category : null,
            'brand' => $request->filled('brand') ? $request->brand : null,
            'search' => $request->filled('search') ? $request->search : null,
        ];

        $products = $this->reports->productSales($from, $to, $filters, 20);
        $categories = $this->reports->categorySales($from, $to, $filters);
        $summary = $this->reports->salesSummary($from, $to, $filters['status']);

        return view('admin.report.item_wise_sales', array_merge($this->filterOptions(), [
            'from' => $from,
            'to' => $to,
            'range' => $range,
            'rangeLabel' => $this->reports->rangeLabel($from, $to, $range),
            'filters' => $filters,
            'products' => $products,
            'categorySales' => $categories,
            'summary' => $summary,
        ]));
    }

    /* =====================================================================
       PRODUCT PERFORMANCE
       ===================================================================== */

    public function product_performance(Request $request)
    {
        [$from, $to, $range] = $this->reports->resolveRange($request);
        $filters = [
            'status' => $request->filled('status') ? $request->status : null,
            'category' => $request->filled('category') ? $request->category : null,
            'brand' => $request->filled('brand') ? $request->brand : null,
        ];

        $performance = $this->reports->productPerformance($from, $to, $filters, 10);
        $categories = $this->reports->categorySales($from, $to, $filters);
        $brands = $this->reports->brandSales($from, $to, $filters);

        return view('admin.reports.product_performance', array_merge($this->filterOptions(), [
            'from' => $from,
            'to' => $to,
            'range' => $range,
            'rangeLabel' => $this->reports->rangeLabel($from, $to, $range),
            'filters' => $filters,
            'best' => $performance['best'],
            'worst' => $performance['worst'],
            'categorySales' => $categories,
            'brandSales' => $brands,
        ]));
    }

    /* =====================================================================
       PROFIT & LOSS
       ===================================================================== */

    public function profit_loss(Request $request)
    {
        [$from, $to, $range] = $this->reports->resolveRange($request);
        $status = $request->filled('status') ? $request->status : null;

        $pl = $this->reports->profitLoss($from, $to, $status);
        $trend = $this->reports->salesTrend($from, $to, $status, 'month');

        return view('admin.reports.profit_loss', array_merge($this->filterOptions(), [
            'from' => $from,
            'to' => $to,
            'range' => $range,
            'rangeLabel' => $this->reports->rangeLabel($from, $to, $range),
            'pl' => $pl,
            'trend' => $trend,
        ]));
    }

    /* =====================================================================
       ORDER REPORT
       ===================================================================== */

    public function orders(Request $request)
    {
        [$from, $to, $range] = $this->reports->resolveRange($request);
        $filters = [
            'status' => $request->filled('status') ? $request->status : null,
            'payment' => $request->filled('payment') ? $request->payment : null,
            'search' => $request->filled('search') ? $request->search : null,
        ];

        $summary = $this->reports->orderSummary($from, $to);
        $orders = $this->reports->ordersList($from, $to, $filters, 20);

        return view('admin.reports.orders', array_merge($this->filterOptions(), [
            'from' => $from,
            'to' => $to,
            'range' => $range,
            'rangeLabel' => $this->reports->rangeLabel($from, $to, $range),
            'filters' => $filters,
            'summary' => $summary,
            'orders' => $orders,
        ]));
    }

    /* =====================================================================
       CUSTOMER REPORT
       ===================================================================== */

    public function customers(Request $request)
    {
        [$from, $to, $range] = $this->reports->resolveRange($request);
        $filters = ['search' => $request->filled('search') ? $request->search : null];
        $perPage = $request->filled('top') ? (int) $request->top : 20;

        $summary = $this->reports->customerSummary($from, $to);
        $customers = $this->reports->customersList($from, $to, $filters, $perPage);

        return view('admin.reports.customers', array_merge($this->filterOptions(), [
            'from' => $from,
            'to' => $to,
            'range' => $range,
            'rangeLabel' => $this->reports->rangeLabel($from, $to, $range),
            'filters' => $filters,
            'summary' => $summary,
            'customers' => $customers,
        ]));
    }

    /* =====================================================================
       PAYMENT REPORT
       ===================================================================== */

    public function payments(Request $request)
    {
        [$from, $to, $range] = $this->reports->resolveRange($request);
        $filters = [
            'payment' => $request->filled('payment') ? $request->payment : null,
            'valid_only' => true,
        ];

        $summary = $this->reports->paymentSummary($from, $to);
        $orders = $this->reports->ordersList($from, $to, $filters, 20);

        return view('admin.reports.payments', array_merge($this->filterOptions(), [
            'from' => $from,
            'to' => $to,
            'range' => $range,
            'rangeLabel' => $this->reports->rangeLabel($from, $to, $range),
            'filters' => $filters,
            'summary' => $summary,
            'orders' => $orders,
        ]));
    }

    /* =====================================================================
       INVENTORY REPORT
       ===================================================================== */

    public function inventory(Request $request)
    {
        $filters = [
            'search' => $request->filled('search') ? $request->search : null,
            'stock' => $request->filled('stock') ? $request->stock : null,
        ];

        $summary = $this->reports->inventorySummary();
        $variants = $this->reports->inventoryList($filters, $summary['threshold'], 20);

        return view('admin.reports.inventory', array_merge($this->filterOptions(), [
            'filters' => $filters,
            'summary' => $summary,
            'variants' => $variants,
        ]));
    }

    /* =====================================================================
       PURCHASE REPORT
       ===================================================================== */

    public function purchases(Request $request)
    {
        [$from, $to, $range] = $this->reports->resolveRange($request);
        $filters = [
            'supplier' => $request->filled('supplier') ? $request->supplier : null,
            'search' => $request->filled('search') ? $request->search : null,
        ];

        $summary = $this->reports->purchaseSummary($from, $to);
        $purchases = $this->reports->purchasesList($from, $to, $filters, 20);
        $supplierWise = $this->reports->supplierWisePurchases($from, $to);
        $productWise = $this->reports->productWisePurchases($from, $to);

        return view('admin.reports.purchases', array_merge($this->filterOptions(), [
            'from' => $from,
            'to' => $to,
            'range' => $range,
            'rangeLabel' => $this->reports->rangeLabel($from, $to, $range),
            'filters' => $filters,
            'summary' => $summary,
            'purchases' => $purchases,
            'supplierWise' => $supplierWise,
            'productWise' => $productWise,
        ]));
    }

    /* =====================================================================
       PURCHASE RETURN REPORT
       ===================================================================== */

    public function purchase_returns(Request $request)
    {
        [$from, $to, $range] = $this->reports->resolveRange($request);
        $filters = ['search' => $request->filled('search') ? $request->search : null];

        $summary = $this->reports->purchaseReturnSummary($from, $to);
        $returns = $this->reports->purchaseReturnsList($from, $to, $filters, 20);

        return view('admin.reports.purchase_returns', array_merge($this->filterOptions(), [
            'from' => $from,
            'to' => $to,
            'range' => $range,
            'rangeLabel' => $this->reports->rangeLabel($from, $to, $range),
            'filters' => $filters,
            'summary' => $summary,
            'returns' => $returns,
        ]));
    }

    /* =====================================================================
       COUPON REPORT
       ===================================================================== */

    public function coupons(Request $request)
    {
        [$from, $to, $range] = $this->reports->resolveRange($request);

        $coupons = $this->reports->couponReport($from, $to);

        return view('admin.reports.coupons', array_merge($this->filterOptions(), [
            'from' => $from,
            'to' => $to,
            'range' => $range,
            'rangeLabel' => $this->reports->rangeLabel($from, $to, $range),
            'coupons' => $coupons,
        ]));
    }

    /* =====================================================================
       SHIPPING REPORT
       ===================================================================== */

    public function shipping(Request $request)
    {
        [$from, $to, $range] = $this->reports->resolveRange($request);

        $summary = $this->reports->shippingSummary($from, $to);

        return view('admin.reports.shipping', array_merge($this->filterOptions(), [
            'from' => $from,
            'to' => $to,
            'range' => $range,
            'rangeLabel' => $this->reports->rangeLabel($from, $to, $range),
            'summary' => $summary,
        ]));
    }

    /* =====================================================================
       EXPORTS
       ===================================================================== */

    /**
     * Legacy export kept for backward compatibility.
     */
    public function exportExcel(Request $request)
    {
        $fileName = 'sales_report_' . now()->format('Y_m_d_His') . '.xlsx';

        return Excel::download(
            new SalesReportExport($request->from_date, $request->to_date, $request->status),
            $fileName
        );
    }

    /**
     * Generic, filter-aware export for every report.
     */
    public function export(Request $request, $report)
    {
        [$from, $to, $range] = $this->reports->resolveRange($request);
        [$headings, $rows] = $this->buildExport($report, $request, $from, $to);

        $fileName = $report . '_report_' . now()->format('Y_m_d_His');

        if ($request->get('format') === 'csv') {
            return Excel::download(new ReportExport($headings, $rows), $fileName . '.csv', \Maatwebsite\Excel\Excel::CSV);
        }

        return Excel::download(new ReportExport($headings, $rows), $fileName . '.xlsx');
    }

    protected function buildExport($report, Request $request, $from, $to): array
    {
        $status = $request->filled('status') ? $request->status : null;

        switch ($report) {
            case 'sales':
                $rows = $this->reports->salesRows($from, $to, $status, 'day');
                $data = $rows->map(fn ($r) => [
                    $r->period,
                    $r->orders,
                    (float) $r->gross,
                    (float) $r->discount,
                    (float) $r->shipping,
                    (float) $r->gross - (float) $r->discount,
                ])->all();
                return [['Date', 'Orders', 'Gross Sales', 'Discount', 'Shipping', 'Net Sales'], $data];

            case 'product_sales':
                $filters = [
                    'status' => $status,
                    'category' => $request->category,
                    'brand' => $request->brand,
                    'search' => $request->search,
                ];
                $rows = $this->reports->productSales($from, $to, $filters, 100000);
                $data = collect($rows->items())->map(function ($r) {
                    $profit = (float) $r->revenue - (float) $r->cost;
                    return [
                        $r->product, $r->sku, $r->category, $r->brand, (int) $r->units,
                        (float) $r->revenue, (float) $r->cost, $profit,
                        (float) $r->revenue > 0 ? round(($profit / (float) $r->revenue) * 100, 2) : 0,
                    ];
                })->all();
                return [['Product', 'SKU', 'Category', 'Brand', 'Units Sold', 'Revenue', 'Cost', 'Profit', 'Margin %'], $data];

            case 'profit_loss':
                $pl = $this->reports->profitLoss($from, $to, $status);
                $data = [
                    ['Gross Sales', $pl['gross']],
                    ['Discounts', $pl['discount']],
                    ['Net Sales', $pl['net']],
                    ['Cost of Goods Sold', $pl['cogs']],
                    ['Gross Profit', $pl['gross_profit']],
                    ['Gross Margin %', $pl['margin']],
                    ['Shipping Collected', $pl['shipping']],
                    ['Collected Total', $pl['grand']],
                ];
                return [['Metric', 'Value'], $data];

            case 'orders':
                $filters = ['status' => $status, 'payment' => $request->payment, 'search' => $request->search];
                $rows = $this->reports->ordersList($from, $to, $filters, 100000);
                $data = collect($rows->items())->map(fn ($o) => [
                    $o->order_id, $o->name, $o->phone, (int) $o->items_count,
                    (float) $o->grand_total, $o->payment_status ? 'Paid' : 'Unpaid',
                    ucfirst($o->status), $o->created_at,
                ])->all();
                return [['Order ID', 'Customer', 'Phone', 'Items', 'Total', 'Payment', 'Status', 'Date'], $data];

            case 'customers':
                $rows = $this->reports->customersList($from, $to, ['search' => $request->search], 100000);
                $data = collect($rows->items())->map(fn ($c) => [
                    $c->name, $c->email, $c->phone, (int) $c->orders_count,
                    (float) $c->total_spent,
                    $c->orders_count > 0 ? round((float) $c->total_spent / (int) $c->orders_count, 2) : 0,
                    $c->last_order,
                ])->all();
                return [['Customer', 'Email', 'Phone', 'Orders', 'Total Spent', 'Avg Order', 'Last Order'], $data];

            case 'payments':
                $filters = ['payment' => $request->payment, 'valid_only' => true];
                $rows = $this->reports->ordersList($from, $to, $filters, 100000);
                $data = collect($rows->items())->map(fn ($o) => [
                    $o->order_id, $o->name, (float) $o->grand_total,
                    $o->payment_status ? 'Paid' : 'Unpaid', $o->created_at,
                ])->all();
                return [['Order ID', 'Customer', 'Amount', 'Payment Status', 'Date'], $data];

            case 'inventory':
                $filters = ['search' => $request->search, 'stock' => $request->stock];
                $rows = $this->reports->inventoryList($filters, 10, 100000);
                $data = collect($rows->items())->map(function ($v) {
                    $qty = (int) $v->qty;
                    $cost = (float) ($v->average_cost ?? $v->purchase_price);
                    $value = $qty > 0 ? $qty * $cost : 0;
                    return [$v->product, $v->sku, $v->category, $qty, $cost, $value];
                })->all();
                return [['Product', 'SKU', 'Category', 'Stock', 'Purchase Cost', 'Stock Value'], $data];

            case 'purchases':
                $filters = ['supplier' => $request->supplier, 'search' => $request->search];
                $rows = $this->reports->purchasesList($from, $to, $filters, 100000);
                $data = collect($rows->items())->map(fn ($p) => [
                    $p->date, $p->id, $p->supplier, (int) $p->items_count, (float) $p->total,
                ])->all();
                return [['Date', 'Purchase ID', 'Supplier', 'Items', 'Amount'], $data];

            case 'purchase_returns':
                $rows = $this->reports->purchaseReturnsList($from, $to, ['search' => $request->search], 100000);
                $data = collect($rows->items())->map(fn ($r) => [
                    $r->return_id, $r->purchase_id, $r->supplier, $r->product,
                    (int) $r->qty, (float) $r->unit_cost, (float) $r->line_amount, $r->date,
                ])->all();
                return [['Return ID', 'Purchase ID', 'Supplier', 'Product', 'Qty', 'Unit Cost', 'Return Amount', 'Date'], $data];

            case 'coupons':
                $rows = $this->reports->couponReport($from, $to);
                $data = $rows->map(fn ($c) => [
                    $c->code, $c->type, (float) $c->discount_amount, (int) $c->used,
                    (float) $c->discount_given, (float) $c->revenue,
                ])->all();
                return [['Coupon', 'Type', 'Value', 'Used', 'Discount Given', 'Revenue Generated'], $data];

            case 'shipping':
                $summary = $this->reports->shippingSummary($from, $to);
                $data = [];
                foreach ($summary['by_status'] as $status => $s) {
                    $data[] = [ucfirst($status), $s['count'], $s['charge'], $s['amount']];
                }
                return [['Status', 'Shipments', 'Shipping Charge', 'Order Value'], $data];

            default:
                return [['Message'], [['Unknown report']]];
        }
    }

    public function exportPdf(Request $request)
    {
        // PDF export placeholder (no PDF dependency is installed in this project).
        return back();
    }
}
