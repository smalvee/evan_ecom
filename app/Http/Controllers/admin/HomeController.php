<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\NewProduct;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $range = $request->get('range', '30d');
        [$from, $to] = $this->resolveRange($range, $request->get('from'), $request->get('to'));

        // KPI
        $totalRevenue = Order::where('status', '!=', 'cancell')->whereBetween('created_at', [$from, $to])->sum('grand_total');
        $totalOrders = Order::whereBetween('created_at', [$from, $to])->count();
        $totalCustomers = User::count();
        $totalProducts = NewProduct::count();

        // Previous period comparison
        $days = (int) $from->diffInDays($to) + 1;
        $prevTo = $from->copy()->subDay()->endOfDay();
        $prevFrom = $from->copy()->subDays($days)->startOfDay();
        $prevRevenue = Order::where('status', '!=', 'cancell')->whereBetween('created_at', [$prevFrom, $prevTo])->sum('grand_total');
        $prevOrders = Order::whereBetween('created_at', [$prevFrom, $prevTo])->count();

        $revenueChange = $prevRevenue > 0 ? round((($totalRevenue - $prevRevenue) / $prevRevenue) * 100, 1) : null;
        $ordersChange = $prevOrders > 0 ? round((($totalOrders - $prevOrders) / $prevOrders) * 100, 1) : null;

        // Order status
        $pendingOrders = Order::where('status', 'pending')->whereBetween('created_at', [$from, $to])->count();
        $confirmedOrders = Order::where('status', 'confirm')->whereBetween('created_at', [$from, $to])->count();
        $shippedOrders = Order::where('status', 'shipped')->whereBetween('created_at', [$from, $to])->count();
        $cancelledOrders = Order::where('status', 'cancell')->whereBetween('created_at', [$from, $to])->count();

        // Free delivery
        $freeDeliveryOrders = Order::whereBetween('created_at', [$from, $to])
            ->whereHas('items', fn ($q) => $q->where('free_delivery', 1))
            ->count();
        $freeDeliveryItems = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('order_items.free_delivery', 1)
            ->whereBetween('orders.created_at', [$from, $to])
            ->count();

        // Product sales (grouped at product level)
        $productSales = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('product_variants', 'product_variants.id', '=', 'order_items.product_id')
            ->join('new_products', 'new_products.id', '=', 'product_variants.product_id')
            ->where('orders.status', '!=', 'cancell')
            ->whereBetween('orders.created_at', [$from, $to])
            ->selectRaw('new_products.id, new_products.name, SUM(order_items.qty) as units, SUM(order_items.total) as revenue')
            ->groupBy('new_products.id', 'new_products.name');

        $topByUnits = (clone $productSales)->orderByDesc('units')->limit(8)->get();
        $topByRevenue = (clone $productSales)->orderByDesc('revenue')->limit(8)->get();

        // Sales by category
        $categorySales = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('product_variants', 'product_variants.id', '=', 'order_items.product_id')
            ->join('new_products', 'new_products.id', '=', 'product_variants.product_id')
            ->leftJoin('categories', 'categories.id', '=', 'new_products.cat_id')
            ->where('orders.status', '!=', 'cancell')
            ->whereBetween('orders.created_at', [$from, $to])
            ->selectRaw('COALESCE(categories.name, "Uncategorized") as category, SUM(order_items.total) as revenue')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('revenue')
            ->limit(8)
            ->get();

        // Top customers
        $topCustomers = Order::query()
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->where('orders.status', '!=', 'cancell')
            ->whereBetween('orders.created_at', [$from, $to])
            ->selectRaw('users.id, users.name, users.phone, COUNT(orders.id) as orders_count, SUM(orders.grand_total) as spent')
            ->groupBy('users.id', 'users.name', 'users.phone')
            ->orderByDesc('spent')
            ->limit(8)
            ->get();

        // Recent orders
        $recentOrders = Order::withCount('items')->latest('id')->take(10)->get();

        // Recent customers
        $recentCustomers = User::withCount(['orders as valid_orders' => fn ($q) => $q->where('status', '!=', 'cancell')])
            ->withSum(['orders as total_spent' => fn ($q) => $q->where('status', '!=', 'cancell')], 'grand_total')
            ->latest('id')
            ->take(8)
            ->get();

        // Inventory
        $threshold = 10;
        $totalVariants = ProductVariant::count();
        $outOfStockVariants = ProductVariant::where(function ($q) {
            $q->whereNull('qty')->orWhereRaw('CAST(qty AS SIGNED) <= 0');
        })->count();
        $lowStockVariants = ProductVariant::whereRaw('CAST(qty AS SIGNED) > 0')
            ->whereRaw('CAST(qty AS SIGNED) <= ?', [$threshold])
            ->count();
        $inStockVariants = $totalVariants - $outOfStockVariants - $lowStockVariants;

        $lowStockProducts = ProductVariant::whereRaw('CAST(qty AS SIGNED) > 0')
            ->whereRaw('CAST(qty AS SIGNED) <= ?', [$threshold])
            ->orderByRaw('CAST(qty AS SIGNED) ASC')
            ->with('product')->take(8)->get();

        $data = compact(
            'range', 'from', 'to',
            'totalRevenue', 'totalOrders', 'totalCustomers', 'totalProducts',
            'revenueChange', 'ordersChange',
            'pendingOrders', 'confirmedOrders', 'shippedOrders', 'cancelledOrders',
            'freeDeliveryOrders', 'freeDeliveryItems',
            'topByUnits', 'topByRevenue', 'categorySales', 'topCustomers',
            'recentOrders', 'recentCustomers',
            'totalVariants', 'inStockVariants', 'lowStockVariants', 'outOfStockVariants',
            'lowStockProducts'
        );

        return view('admin.new_dashboard', $data);
    }

    private function resolveRange($range, $fromInput, $toInput)
    {
        $now = Carbon::now();
        $to = $now->copy()->endOfDay();

        switch ($range) {
            case 'today':
                $from = $now->copy()->startOfDay();
                break;
            case 'yesterday':
                $from = $now->copy()->subDay()->startOfDay();
                $to = $now->copy()->subDay()->endOfDay();
                break;
            case '7d':
                $from = $now->copy()->subDays(6)->startOfDay();
                break;
            case 'this_month':
                $from = $now->copy()->startOfMonth();
                break;
            case 'last_month':
                $from = $now->copy()->subMonth()->startOfMonth();
                $to = $now->copy()->subMonth()->endOfMonth();
                break;
            case 'this_year':
                $from = $now->copy()->startOfYear();
                break;
            case 'custom':
                $from = $fromInput ? Carbon::parse($fromInput)->startOfDay() : $now->copy()->subDays(29)->startOfDay();
                $to = $toInput ? Carbon::parse($toInput)->endOfDay() : $now->copy()->endOfDay();
                break;
            case '30d':
            default:
                $from = $now->copy()->subDays(29)->startOfDay();
                break;
        }

        if ($from->gt($to)) {
            [$from, $to] = [$to, $from];
        }

        return [$from, $to];
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
