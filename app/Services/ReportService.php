<?php

namespace App\Services;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Central reporting service.
 *
 * All financial metrics are derived from the application's real data.
 *
 * SALES RULES (documented):
 *  - A "valid sale" is an order whose status is NOT "cancell" (pending, confirm, shipped).
 *  - Gross Sales   = SUM(orders.subtotal)
 *  - Discount      = SUM(orders.discount)             (coupon/order-level discount applied to the total)
 *  - Shipping      = SUM(orders.shipping)
 *  - Net Sales     = Gross Sales - Discount            (product revenue, excludes shipping)
 *  - Collected     = SUM(orders.grand_total) = Net Sales + Shipping
 *  - COGS          = SUM(product_variants.purchase_price * order_items.qty)
 *  - Gross Profit  = Net Sales - COGS
 *
 *  IMPORTANT: orders.additional_discount is NOT subtracted from grand_total by the application
 *  (admin order form computes grand_total = subtotal + shipping, and subtotal is already net of
 *  line-item discounts). It is therefore excluded from all financial calculations to avoid
 *  double-counting. Net Sales + Shipping always reconciles to SUM(grand_total).
 *
 *  - Refunds are NOT tracked by this application (no refund table/field).
 *  - Payment: only orders.payment_status (0 = unpaid, 1 = paid) exists. No payment method is stored.
 */
class ReportService
{
    public const EXCLUDED_STATUS = 'cancell';

    public function statusOptions(): array
    {
        return [
            'pending' => 'Pending',
            'confirm' => 'Confirmed',
            'shipped' => 'Delivered',
            'cancell' => 'Cancelled',
        ];
    }

    public function paymentOptions(): array
    {
        return [
            '1' => 'Paid',
            '0' => 'Unpaid',
        ];
    }

    public function rangeOptions(): array
    {
        return [
            'all' => 'All Time',
            'today' => 'Today',
            'yesterday' => 'Yesterday',
            'this_week' => 'This Week',
            'last_week' => 'Last Week',
            'this_month' => 'This Month',
            'last_month' => 'Last Month',
            'this_year' => 'This Year',
            'custom' => 'Custom Range',
        ];
    }

    /**
     * Resolve the selected date range.
     *
     * @return array{0: ?Carbon, 1: ?Carbon, 2: string}
     */
    public function resolveRange(Request $request): array
    {
        $range = $request->get('range', 'all');
        $now = Carbon::now();
        $from = null;
        $to = null;

        switch ($range) {
            case 'today':
                $from = $now->copy()->startOfDay();
                $to = $now->copy()->endOfDay();
                break;
            case 'yesterday':
                $from = $now->copy()->subDay()->startOfDay();
                $to = $now->copy()->subDay()->endOfDay();
                break;
            case 'this_week':
                $from = $now->copy()->startOfWeek();
                $to = $now->copy()->endOfWeek();
                break;
            case 'last_week':
                $from = $now->copy()->subWeek()->startOfWeek();
                $to = $now->copy()->subWeek()->endOfWeek();
                break;
            case 'this_month':
                $from = $now->copy()->startOfMonth();
                $to = $now->copy()->endOfMonth();
                break;
            case 'last_month':
                $from = $now->copy()->subMonth()->startOfMonth();
                $to = $now->copy()->subMonth()->endOfMonth();
                break;
            case 'this_year':
                $from = $now->copy()->startOfYear();
                $to = $now->copy()->endOfYear();
                break;
            case 'custom':
                $from = $request->filled('from_date') ? Carbon::parse($request->from_date)->startOfDay() : null;
                $to = $request->filled('to_date') ? Carbon::parse($request->to_date)->endOfDay() : null;
                break;
            case 'all':
            default:
                $range = 'all';
        }

        if ($from && $to && $from->gt($to)) {
            [$from, $to] = [$to, $from];
        }

        return [$from, $to, $range];
    }

    /**
     * @return array{0: ?Carbon, 1: ?Carbon}
     */
    public function previousPeriod(?Carbon $from, ?Carbon $to): array
    {
        if (!$from || !$to) {
            return [null, null];
        }
        $days = $from->diffInDays($to) + 1;

        return [$from->copy()->subDays($days)->startOfDay(), $from->copy()->subDay()->endOfDay()];
    }

    public function rangeLabel(?Carbon $from, ?Carbon $to, string $range): string
    {
        if ($range === 'all' || !$from || !$to) {
            return 'All Time';
        }

        return $from->format('d M Y') . ' – ' . $to->format('d M Y');
    }

    /**
     * Apply the standard order scope (date + status + payment).
     */
    protected function scopeOrders($query, ?Carbon $from, ?Carbon $to, ?string $status = null, $payment = null)
    {
        if ($from) {
            $query->where('orders.created_at', '>=', $from);
        }
        if ($to) {
            $query->where('orders.created_at', '<=', $to);
        }

        if ($status !== null && $status !== '') {
            $query->where('orders.status', $status);
        } else {
            $query->where('orders.status', '!=', self::EXCLUDED_STATUS);
        }

        if ($payment !== null && $payment !== '') {
            $query->where('orders.payment_status', (int) $payment);
        }

        return $query;
    }

    /* =====================================================================
       SALES
       ===================================================================== */

    public function salesSummary(?Carbon $from, ?Carbon $to, ?string $status = null, $payment = null): array
    {
        $base = $this->scopeOrders(Order::query(), $from, $to, $status, $payment);

        $row = (clone $base)->selectRaw('
            COUNT(*) as orders,
            COALESCE(SUM(subtotal), 0) as gross,
            COALESCE(SUM(discount), 0) as discount,
            COALESCE(SUM(additional_discount), 0) as additional_discount,
            COALESCE(SUM(shipping), 0) as shipping,
            COALESCE(SUM(grand_total), 0) as grand
        ')->first();

        $cogs = (clone $base)
            ->join('order_items', 'order_items.order_id', '=', 'orders.id')
            ->join('product_variants', 'product_variants.id', '=', 'order_items.product_id')
            ->selectRaw('COALESCE(SUM(CAST(COALESCE(order_items.cost_price, product_variants.purchase_price) AS DECIMAL(12,2)) * order_items.qty), 0) as cogs')
            ->value('cogs');

        $discount = (float) $row->discount;
        $net = (float) $row->gross - $discount;
        $cogs = (float) $cogs;
        $grossProfit = $net - $cogs;

        // Cancelled orders (excluded from sales) — surfaced separately.
        $cancelledQuery = Order::query()->where('orders.status', self::EXCLUDED_STATUS);
        if ($from) {
            $cancelledQuery->where('orders.created_at', '>=', $from);
        }
        if ($to) {
            $cancelledQuery->where('orders.created_at', '<=', $to);
        }
        $cancelled = $cancelledQuery->selectRaw('COUNT(*) as orders, COALESCE(SUM(grand_total), 0) as amount')->first();

        return [
            'orders' => (int) $row->orders,
            'gross' => (float) $row->gross,
            'discount' => $discount,
            'shipping' => (float) $row->shipping,
            'net' => $net,
            'grand' => (float) $row->grand,
            'aov' => $row->orders > 0 ? (float) $row->grand / (int) $row->orders : 0,
            'cogs' => $cogs,
            'gross_profit' => $grossProfit,
            'margin' => $net > 0 ? round(($grossProfit / $net) * 100, 2) : 0,
            'cancelled_orders' => (int) $cancelled->orders,
            'cancelled_amount' => (float) $cancelled->amount,
        ];
    }

    public function salesTrend(?Carbon $from, ?Carbon $to, ?string $status = null, string $groupBy = 'day')
    {
        $expr = match ($groupBy) {
            'month' => "DATE_FORMAT(orders.created_at, '%Y-%m')",
            'week' => "DATE_FORMAT(orders.created_at, '%x-W%v')",
            default => "DATE(orders.created_at)",
        };

        return $this->scopeOrders(Order::query(), $from, $to, $status)
            ->selectRaw("$expr as period,
                COUNT(*) as orders,
                COALESCE(SUM(subtotal), 0) as gross,
                COALESCE(SUM(discount), 0) as discount,
                COALESCE(SUM(additional_discount), 0) as additional_discount,
                COALESCE(SUM(shipping), 0) as shipping,
                COALESCE(SUM(grand_total), 0) as grand")
            ->groupBy('period')
            ->orderBy('period')
            ->get();
    }

    public function salesRows(?Carbon $from, ?Carbon $to, ?string $status = null, string $groupBy = 'day')
    {
        return $this->salesTrend($from, $to, $status, $groupBy);
    }

    /* =====================================================================
       PRODUCT SALES / PERFORMANCE
       ===================================================================== */

    protected function productSalesQuery(?Carbon $from, ?Carbon $to, array $f = [])
    {
        $q = DB::table('order_items as oi')
            ->join('orders as o', 'o.id', '=', 'oi.order_id')
            ->leftJoin('product_variants as pv', 'pv.id', '=', 'oi.product_id')
            ->leftJoin('new_products as np', 'np.id', '=', 'pv.product_id')
            ->leftJoin('categories as c', 'c.id', '=', 'np.cat_id')
            ->leftJoin('brands as b', 'b.id', '=', 'np.brand_id');

        if (!empty($f['status'])) {
            $q->where('o.status', $f['status']);
        } else {
            $q->where('o.status', '!=', self::EXCLUDED_STATUS);
        }
        if ($from) {
            $q->where('o.created_at', '>=', $from);
        }
        if ($to) {
            $q->where('o.created_at', '<=', $to);
        }
        if (!empty($f['category'])) {
            $q->where('np.cat_id', $f['category']);
        }
        if (!empty($f['brand'])) {
            $q->where('np.brand_id', $f['brand']);
        }
        if (!empty($f['search'])) {
            $q->where(function ($x) use ($f) {
                $x->where('np.name', 'like', '%' . $f['search'] . '%')
                    ->orWhere('pv.sku', 'like', '%' . $f['search'] . '%')
                    ->orWhere('oi.name', 'like', '%' . $f['search'] . '%');
            });
        }

        return $q;
    }

    public function productSales(?Carbon $from, ?Carbon $to, array $f = [], int $perPage = 20)
    {
        return $this->productSalesQuery($from, $to, $f)
            ->selectRaw("
                np.id as product_id,
                COALESCE(np.name, oi.name) as product,
                pv.sku as sku,
                c.name as category,
                b.name as brand,
                SUM(oi.qty) as units,
                COALESCE(SUM(oi.total), 0) as revenue,
                COALESCE(SUM(oi.discount), 0) as item_discount,
                COALESCE(SUM(CAST(COALESCE(oi.cost_price, pv.purchase_price) AS DECIMAL(12,2)) * oi.qty), 0) as cost
            ")
            ->groupBy('np.id', 'np.name', 'oi.name', 'pv.sku', 'c.name', 'b.name')
            ->orderByDesc('revenue')
            ->paginate($perPage)
            ->appends(request()->query());
    }

    public function productPerformance(?Carbon $from, ?Carbon $to, array $f = [], int $limit = 10)
    {
        $base = $this->productSalesQuery($from, $to, $f);

        $best = (clone $base)
            ->selectRaw("
                np.id as product_id,
                COALESCE(np.name, oi.name) as product,
                SUM(oi.qty) as units,
                COALESCE(SUM(oi.total), 0) as revenue,
                COALESCE(SUM(oi.total) - SUM(CAST(COALESCE(oi.cost_price, pv.purchase_price) AS DECIMAL(12,2)) * oi.qty), 0) as profit
            ")
            ->groupBy('np.id', 'np.name', 'oi.name')
            ->orderByDesc('units')
            ->limit($limit)
            ->get();

        $worst = (clone $base)
            ->selectRaw("
                np.id as product_id,
                COALESCE(np.name, oi.name) as product,
                SUM(oi.qty) as units,
                COALESCE(SUM(oi.total), 0) as revenue
            ")
            ->groupBy('np.id', 'np.name', 'oi.name')
            ->orderBy('units')
            ->limit($limit)
            ->get();

        return ['best' => $best, 'worst' => $worst];
    }

    public function categorySales(?Carbon $from, ?Carbon $to, array $f = [])
    {
        return $this->productSalesQuery($from, $to, $f)
            ->selectRaw("
                COALESCE(c.name, 'Uncategorized') as category,
                SUM(oi.qty) as units,
                COALESCE(SUM(oi.total), 0) as revenue,
                COALESCE(SUM(CAST(COALESCE(oi.cost_price, pv.purchase_price) AS DECIMAL(12,2)) * oi.qty), 0) as cost
            ")
            ->groupBy('c.id', 'c.name')
            ->orderByDesc('revenue')
            ->get();
    }

    public function brandSales(?Carbon $from, ?Carbon $to, array $f = [])
    {
        return $this->productSalesQuery($from, $to, $f)
            ->selectRaw("
                COALESCE(b.name, 'No Brand') as brand,
                COUNT(DISTINCT np.id) as products,
                SUM(oi.qty) as units,
                COALESCE(SUM(oi.total), 0) as revenue,
                COALESCE(SUM(CAST(COALESCE(oi.cost_price, pv.purchase_price) AS DECIMAL(12,2)) * oi.qty), 0) as cost
            ")
            ->groupBy('b.id', 'b.name')
            ->orderByDesc('revenue')
            ->get();
    }

    public function topProducts(?Carbon $from, ?Carbon $to, int $limit = 8)
    {
        return $this->productSalesQuery($from, $to, [])
            ->selectRaw("
                COALESCE(np.name, oi.name) as product,
                SUM(oi.qty) as units,
                COALESCE(SUM(oi.total), 0) as revenue
            ")
            ->groupBy('np.id', 'np.name', 'oi.name')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get();
    }

    /* =====================================================================
       PROFIT & LOSS
       ===================================================================== */

    public function profitLoss(?Carbon $from, ?Carbon $to, ?string $status = null)
    {
        return $this->salesSummary($from, $to, $status);
    }

    /* =====================================================================
       ORDERS
       ===================================================================== */

    public function orderSummary(?Carbon $from, ?Carbon $to): array
    {
        $rows = Order::query()
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to))
            ->selectRaw('status, COUNT(*) as c, COALESCE(SUM(grand_total), 0) as amount')
            ->groupBy('status')
            ->get();

        $byStatus = [];
        foreach ($rows as $r) {
            $byStatus[$r->status] = ['count' => (int) $r->c, 'amount' => (float) $r->amount];
        }

        $payment = Order::query()
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to))
            ->selectRaw('payment_status, COUNT(*) as c, COALESCE(SUM(grand_total), 0) as amount')
            ->groupBy('payment_status')
            ->get()
            ->keyBy('payment_status');

        $total = array_sum(array_column($byStatus, 'count'));

        return [
            'total' => $total,
            'by_status' => $byStatus,
            'paid' => [
                'count' => (int) ($payment[1]->c ?? 0),
                'amount' => (float) ($payment[1]->amount ?? 0),
            ],
            'unpaid' => [
                'count' => (int) ($payment[0]->c ?? 0),
                'amount' => (float) ($payment[0]->amount ?? 0),
            ],
        ];
    }

    public function ordersList(?Carbon $from, ?Carbon $to, array $f = [], int $perPage = 20)
    {
        $q = Order::query()->join('users', 'users.id', '=', 'orders.user_id')
            ->select('orders.*', 'users.name as customer_name', 'users.email as customer_email');

        if ($from) {
            $q->where('orders.created_at', '>=', $from);
        }
        if ($to) {
            $q->where('orders.created_at', '<=', $to);
        }
        if (!empty($f['valid_only'])) {
            $q->where('orders.status', '!=', self::EXCLUDED_STATUS);
        }
        if (!empty($f['status'])) {
            $q->where('orders.status', $f['status']);
        }
        if (isset($f['payment']) && $f['payment'] !== '') {
            $q->where('orders.payment_status', (int) $f['payment']);
        }
        if (!empty($f['search'])) {
            $q->where(function ($x) use ($f) {
                $x->where('orders.order_id', 'like', '%' . $f['search'] . '%')
                    ->orWhere('orders.name', 'like', '%' . $f['search'] . '%')
                    ->orWhere('orders.phone', 'like', '%' . $f['search'] . '%')
                    ->orWhere('users.name', 'like', '%' . $f['search'] . '%');
            });
        }

        return $q->withCount('items')->orderByDesc('orders.created_at')->paginate($perPage)->appends(request()->query());
    }

    /* =====================================================================
       CUSTOMERS
       ===================================================================== */

    public function customerSummary(?Carbon $from, ?Carbon $to): array
    {
        $totalCustomers = DB::table('users')->where('role', '!=', 2)->count();

        $newCustomers = DB::table('users')->where('role', '!=', 2)
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to))
            ->count();

        $withOrders = DB::table('orders')->where('status', '!=', self::EXCLUDED_STATUS)
            ->distinct()->count('user_id');

        $active = DB::table('orders')->where('status', '!=', self::EXCLUDED_STATUS)
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to))
            ->distinct()->count('user_id');

        $returning = DB::table('orders')->where('status', '!=', self::EXCLUDED_STATUS)
            ->select('user_id')->groupBy('user_id')->havingRaw('COUNT(*) > 1')->get()->count();

        return [
            'total' => $totalCustomers,
            'new' => $newCustomers,
            'with_orders' => $withOrders,
            'active' => $active,
            'returning' => $returning,
        ];
    }

    public function customersList(?Carbon $from, ?Carbon $to, array $f = [], int $perPage = 20)
    {
        $q = DB::table('users as u')
            ->where('u.role', '!=', 2)
            ->leftJoin('orders as o', function ($join) use ($from, $to) {
                $join->on('o.user_id', '=', 'u.id')
                    ->where('o.status', '!=', self::EXCLUDED_STATUS);
                if ($from) {
                    $join->where('o.created_at', '>=', $from);
                }
                if ($to) {
                    $join->where('o.created_at', '<=', $to);
                }
            });

        if (!empty($f['search'])) {
            $q->where(function ($x) use ($f) {
                $x->where('u.name', 'like', '%' . $f['search'] . '%')
                    ->orWhere('u.email', 'like', '%' . $f['search'] . '%')
                    ->orWhere('u.phone', 'like', '%' . $f['search'] . '%');
            });
        }

        return $q->selectRaw('
                u.id, u.name, u.email, u.phone, u.created_at,
                COUNT(o.id) as orders_count,
                COALESCE(SUM(o.grand_total), 0) as total_spent,
                MAX(o.created_at) as last_order
            ')
            ->groupBy('u.id', 'u.name', 'u.email', 'u.phone', 'u.created_at')
            ->orderByDesc('total_spent')
            ->paginate($perPage)
            ->appends(request()->query());
    }

    /* =====================================================================
       PAYMENTS
       ===================================================================== */

    public function paymentSummary(?Carbon $from, ?Carbon $to): array
    {
        $rows = Order::query()
            ->where('status', '!=', self::EXCLUDED_STATUS)
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to))
            ->selectRaw('payment_status, COUNT(*) as c, COALESCE(SUM(grand_total), 0) as amount')
            ->groupBy('payment_status')
            ->get()
            ->keyBy('payment_status');

        return [
            'paid' => ['count' => (int) ($rows[1]->c ?? 0), 'amount' => (float) ($rows[1]->amount ?? 0)],
            'unpaid' => ['count' => (int) ($rows[0]->c ?? 0), 'amount' => (float) ($rows[0]->amount ?? 0)],
        ];
    }

    /* =====================================================================
       INVENTORY
       ===================================================================== */

    public function inventorySummary(int $threshold = 10): array
    {
        $row = DB::table('product_variants as pv')
            ->leftJoin('new_products as np', 'np.id', '=', 'pv.product_id')
            ->selectRaw("
                COUNT(*) as variants,
                COUNT(DISTINCT np.id) as products,
                COALESCE(SUM(CAST(pv.qty AS SIGNED)), 0) as units,
                COALESCE(SUM(CASE WHEN CAST(pv.qty AS SIGNED) > 0 THEN CAST(pv.qty AS SIGNED) * CAST(COALESCE(pv.average_cost, pv.purchase_price) AS DECIMAL(12,2)) ELSE 0 END), 0) as stock_value,
                SUM(CASE WHEN CAST(pv.qty AS SIGNED) <= 0 THEN 1 ELSE 0 END) as out_of_stock,
                SUM(CASE WHEN CAST(pv.qty AS SIGNED) > 0 AND CAST(pv.qty AS SIGNED) <= $threshold THEN 1 ELSE 0 END) as low_stock
            ")
            ->first();

        return [
            'variants' => (int) $row->variants,
            'products' => (int) $row->products,
            'units' => (int) $row->units,
            'stock_value' => (float) $row->stock_value,
            'out_of_stock' => (int) $row->out_of_stock,
            'low_stock' => (int) $row->low_stock,
            'threshold' => $threshold,
        ];
    }

    public function inventoryList(array $f = [], int $threshold = 10, int $perPage = 20)
    {
        $q = DB::table('product_variants as pv')
            ->leftJoin('new_products as np', 'np.id', '=', 'pv.product_id')
            ->leftJoin('categories as c', 'c.id', '=', 'np.cat_id');

        if (!empty($f['search'])) {
            $q->where(function ($x) use ($f) {
                $x->where('np.name', 'like', '%' . $f['search'] . '%')
                    ->orWhere('pv.sku', 'like', '%' . $f['search'] . '%');
            });
        }
        if (!empty($f['stock'])) {
            if ($f['stock'] === 'out') {
                $q->whereRaw('CAST(pv.qty AS SIGNED) <= 0');
            } elseif ($f['stock'] === 'low') {
                $q->whereRaw("CAST(pv.qty AS SIGNED) > 0 AND CAST(pv.qty AS SIGNED) <= $threshold");
            } elseif ($f['stock'] === 'in') {
                $q->whereRaw("CAST(pv.qty AS SIGNED) > $threshold");
            }
        }

        return $q->selectRaw("
                pv.id, pv.sku, pv.qty, pv.purchase_price, pv.average_cost, pv.selling_price,
                np.name as product, c.name as category
            ")
            ->orderByRaw('CAST(pv.qty AS SIGNED) ASC')
            ->paginate($perPage)
            ->appends(request()->query());
    }

    /* =====================================================================
       PURCHASES
       ===================================================================== */

    public function purchaseSummary(?Carbon $from, ?Carbon $to): array
    {
        $purchases = DB::table('purchases')
            ->when($from, fn ($q) => $q->where('date', '>=', $from->toDateString()))
            ->when($to, fn ($q) => $q->where('date', '<=', $to->toDateString()))
            ->selectRaw('COUNT(*) as c, COALESCE(SUM(total), 0) as amount')
            ->first();

        $returns = DB::table('purchase_returns as pr')
            ->join('purchases as p', 'p.id', '=', 'pr.purchase_id')
            ->when($from, fn ($q) => $q->where('p.date', '>=', $from->toDateString()))
            ->when($to, fn ($q) => $q->where('p.date', '<=', $to->toDateString()))
            ->selectRaw('COUNT(*) as c, COALESCE(SUM(pr.return_amount), 0) as amount')
            ->first();

        return [
            'count' => (int) $purchases->c,
            'amount' => (float) $purchases->amount,
            'return_count' => (int) $returns->c,
            'return_amount' => (float) $returns->amount,
            'net' => (float) $purchases->amount - (float) $returns->amount,
        ];
    }

    public function purchasesList(?Carbon $from, ?Carbon $to, array $f = [], int $perPage = 20)
    {
        $q = DB::table('purchases as p')
            ->leftJoin('suppliers as s', 's.id', '=', 'p.supplier_id');

        if ($from) {
            $q->where('p.date', '>=', $from->toDateString());
        }
        if ($to) {
            $q->where('p.date', '<=', $to->toDateString());
        }
        if (!empty($f['supplier'])) {
            $q->where('p.supplier_id', $f['supplier']);
        }
        if (!empty($f['search'])) {
            $q->where(function ($x) use ($f) {
                $x->where('s.name', 'like', '%' . $f['search'] . '%')
                    ->orWhere('p.id', 'like', '%' . $f['search'] . '%');
            });
        }

        return $q->selectRaw('
                p.id, p.date, p.total, p.supplier_id,
                COALESCE(s.name, "-") as supplier,
                (SELECT COUNT(*) FROM purchase_items pi WHERE pi.purchase_id = p.id) as items_count,
                (SELECT COALESCE(SUM(pi.qty), 0) FROM purchase_items pi WHERE pi.purchase_id = p.id) as qty
            ')
            ->orderByDesc('p.date')
            ->paginate($perPage)
            ->appends(request()->query());
    }

    public function supplierWisePurchases(?Carbon $from, ?Carbon $to)
    {
        return DB::table('purchases as p')
            ->leftJoin('suppliers as s', 's.id', '=', 'p.supplier_id')
            ->when($from, fn ($q) => $q->where('p.date', '>=', $from->toDateString()))
            ->when($to, fn ($q) => $q->where('p.date', '<=', $to->toDateString()))
            ->selectRaw('
                COALESCE(s.name, "-") as supplier,
                COUNT(p.id) as purchases,
                COALESCE(SUM(p.total), 0) as amount,
                COALESCE((SELECT SUM(pr.return_amount) FROM purchase_returns pr JOIN purchases p2 ON p2.id = pr.purchase_id WHERE p2.supplier_id = p.supplier_id), 0) as return_amount
            ')
            ->groupBy('p.supplier_id', 's.name')
            ->orderByDesc('amount')
            ->get();
    }

    public function productWisePurchases(?Carbon $from, ?Carbon $to)
    {
        return DB::table('purchase_items as pi')
            ->join('purchases as p', 'p.id', '=', 'pi.purchase_id')
            ->leftJoin('product_variants as pv', 'pv.id', '=', 'pi.variant_id')
            ->leftJoin('new_products as np', 'np.id', '=', 'pv.product_id')
            ->when($from, fn ($q) => $q->where('p.date', '>=', $from->toDateString()))
            ->when($to, fn ($q) => $q->where('p.date', '<=', $to->toDateString()))
            ->selectRaw('
                COALESCE(np.name, pi.p_name) as product,
                pv.sku as sku,
                SUM(pi.qty) as qty,
                COALESCE(SUM(pi.qty * pi.unit_cost), 0) as cost,
                COALESCE((SELECT SUM(pri.qty) FROM purchase_return_items pri WHERE pri.variant_id = pi.variant_id), 0) as returned_qty
            ')
            ->groupBy('pi.variant_id', 'np.name', 'pi.p_name', 'pv.sku')
            ->orderByDesc('cost')
            ->get();
    }

    /* =====================================================================
       PURCHASE RETURNS
       ===================================================================== */

    public function purchaseReturnSummary(?Carbon $from, ?Carbon $to): array
    {
        $row = DB::table('purchase_returns as pr')
            ->join('purchases as p', 'p.id', '=', 'pr.purchase_id')
            ->when($from, fn ($q) => $q->where('p.date', '>=', $from->toDateString()))
            ->when($to, fn ($q) => $q->where('p.date', '<=', $to->toDateString()))
            ->selectRaw('COUNT(*) as c, COALESCE(SUM(pr.return_amount), 0) as amount')
            ->first();

        $qty = DB::table('purchase_return_items as pri')
            ->join('purchase_returns as pr', 'pr.id', '=', 'pri.purchase_return_id')
            ->join('purchases as p', 'p.id', '=', 'pr.purchase_id')
            ->when($from, fn ($q) => $q->where('p.date', '>=', $from->toDateString()))
            ->when($to, fn ($q) => $q->where('p.date', '<=', $to->toDateString()))
            ->sum('pri.qty');

        return [
            'count' => (int) $row->c,
            'amount' => (float) $row->amount,
            'qty' => (int) $qty,
        ];
    }

    public function purchaseReturnsList(?Carbon $from, ?Carbon $to, array $f = [], int $perPage = 20)
    {
        $q = DB::table('purchase_return_items as pri')
            ->join('purchase_returns as pr', 'pr.id', '=', 'pri.purchase_return_id')
            ->join('purchases as p', 'p.id', '=', 'pr.purchase_id')
            ->leftJoin('suppliers as s', 's.id', '=', 'p.supplier_id')
            ->leftJoin('product_variants as pv', 'pv.id', '=', 'pri.variant_id')
            ->leftJoin('new_products as np', 'np.id', '=', 'pv.product_id');

        if ($from) {
            $q->where('p.date', '>=', $from->toDateString());
        }
        if ($to) {
            $q->where('p.date', '<=', $to->toDateString());
        }
        if (!empty($f['search'])) {
            $q->where(function ($x) use ($f) {
                $x->where('np.name', 'like', '%' . $f['search'] . '%')
                    ->orWhere('pv.sku', 'like', '%' . $f['search'] . '%')
                    ->orWhere('s.name', 'like', '%' . $f['search'] . '%');
            });
        }

        return $q->selectRaw('
                pri.id, pr.id as return_id, pr.purchase_id, p.date,
                COALESCE(s.name, "-") as supplier,
                COALESCE(np.name, pv.sku) as product,
                pri.qty, pri.unit_cost,
                (pri.qty * pri.unit_cost) as line_amount
            ')
            ->orderByDesc('pr.id')
            ->paginate($perPage)
            ->appends(request()->query());
    }

    /* =====================================================================
       COUPONS
       ===================================================================== */

    public function couponReport(?Carbon $from, ?Carbon $to)
    {
        $orders = DB::table('orders')
            ->whereNotNull('coupon_code')
            ->where('coupon_code', '!=', '')
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to))
            ->selectRaw('
                coupon_code,
                COUNT(*) as used,
                COALESCE(SUM(discount), 0) as discount,
                COALESCE(SUM(grand_total), 0) as revenue
            ')
            ->groupBy('coupon_code')
            ->get()
            ->keyBy('coupon_code');

        return DB::table('discount_coupons as dc')
            ->selectRaw('dc.id, dc.code, dc.type, dc.discount_amount, dc.status, dc.starts_at, dc.expires_at')
            ->orderBy('dc.code')
            ->get()
            ->map(function ($c) use ($orders) {
                $o = $orders[$c->code] ?? null;
                $c->used = (int) ($o->used ?? 0);
                $c->discount_given = (float) ($o->discount ?? 0);
                $c->revenue = (float) ($o->revenue ?? 0);
                return $c;
            });
    }

    /* =====================================================================
       SHIPPING
       ===================================================================== */

    public function shippingSummary(?Carbon $from, ?Carbon $to): array
    {
        $rows = Order::query()
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to))
            ->selectRaw('status, COUNT(*) as c, COALESCE(SUM(shipping), 0) as charge, COALESCE(SUM(grand_total), 0) as amount')
            ->groupBy('status')
            ->get();

        $byStatus = [];
        $totalCharge = 0;
        $totalOrders = 0;
        foreach ($rows as $r) {
            $byStatus[$r->status] = [
                'count' => (int) $r->c,
                'charge' => (float) $r->charge,
                'amount' => (float) $r->amount,
            ];
            $totalCharge += (float) $r->charge;
            $totalOrders += (int) $r->c;
        }

        return [
            'total' => $totalOrders,
            'by_status' => $byStatus,
            'total_charge' => $totalCharge,
        ];
    }

    /* =====================================================================
       DASHBOARD
       ===================================================================== */

    public function dashboard(?Carbon $from, ?Carbon $to): array
    {
        $sales = $this->salesSummary($from, $to);
        $prev = $this->previousPeriod($from, $to);
        $prevSales = ($prev[0] && $prev[1]) ? $this->salesSummary($prev[0], $prev[1]) : null;

        return [
            'sales' => $sales,
            'prev_sales' => $prevSales,
            'orders' => $this->orderSummary($from, $to),
            'inventory' => $this->inventorySummary(),
            'customers' => $this->customerSummary($from, $to),
            'top_products' => $this->topProducts($from, $to, 6),
            'categories' => $this->categorySales($from, $to)->take(6),
        ];
    }

    public function growth($current, $previous): ?float
    {
        if ($previous === null || (float) $previous == 0.0) {
            return null;
        }

        return round((($current - $previous) / $previous) * 100, 2);
    }
}
