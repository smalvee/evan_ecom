@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        {{-- Page header + date filter --}}
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                </ul>
                <h4 class="a-page-title">Dashboard</h4>
                <p class="a-page-desc">Overview of your store performance and operations.</p>
            </div>
            <div class="a-actions">
                <form method="GET" class="d-flex flex-wrap align-items-center gap-2">
                    <select name="range" class="form-select" style="width: auto;" onchange="this.form.submit()">
                        <option value="today" {{ $range == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="7d" {{ $range == '7d' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="30d" {{ $range == '30d' ? 'selected' : '' }}>Last 30 Days</option>
                        <option value="this_month" {{ $range == 'this_month' ? 'selected' : '' }}>This Month</option>
                        <option value="last_month" {{ $range == 'last_month' ? 'selected' : '' }}>Last Month</option>
                        <option value="this_year" {{ $range == 'this_year' ? 'selected' : '' }}>This Year</option>
                        <option value="custom" {{ $range == 'custom' ? 'selected' : '' }}>Custom</option>
                    </select>
                    <input type="date" name="from" class="form-control" style="width: auto;"
                        value="{{ $from->toDateString() }}">
                    <input type="date" name="to" class="form-control" style="width: auto;"
                        value="{{ $to->toDateString() }}">
                    <button type="submit" class="btn btn-theme">Apply</button>
                </form>
            </div>
        </div>

        {{-- KPI cards --}}
        <div class="row g-3">
            <div class="col-sm-6 col-xxl-3 col-lg-6">
                <div class="a-stat-card d-flex justify-content-between">
                    <div>
                        <div class="a-stat-label">
                            Total Revenue
                            <i class="ri-information-line text-muted" style="cursor: help;"
                                data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true"
                                title="<strong>How Total Revenue is calculated</strong><br>Sum of <code>grand_total</code> for all orders that are <b>not cancelled</b> within the selected date range."></i>
                        </div>
                        <div class="a-stat-value">৳ {{ number_format($totalRevenue, 2) }}</div>
                        @if ($revenueChange !== null)
                            <span class="a-stat-delta {{ $revenueChange >= 0 ? 'up' : 'down' }}">
                                {{ $revenueChange >= 0 ? '↑' : '↓' }} {{ abs($revenueChange) }}% vs previous
                            </span>
                        @else
                            <span class="a-stat-delta flat">No previous-period data</span>
                        @endif
                    </div>
                    <div class="a-stat-icon" style="background: var(--a-primary-soft); color: var(--a-primary);">
                        <i class="ri-money-dollar-circle-line"></i>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xxl-3 col-lg-6">
                <div class="a-stat-card d-flex justify-content-between">
                    <div>
                        <div class="a-stat-label">Total Orders</div>
                        <div class="a-stat-value">{{ $totalOrders }}</div>
                        @if ($ordersChange !== null)
                            <span class="a-stat-delta {{ $ordersChange >= 0 ? 'up' : 'down' }}">
                                {{ $ordersChange >= 0 ? '↑' : '↓' }} {{ abs($ordersChange) }}% vs previous
                            </span>
                        @else
                            <span class="a-stat-delta flat">No previous-period data</span>
                        @endif
                    </div>
                    <div class="a-stat-icon" style="background: var(--a-info-soft); color: var(--a-info);">
                        <i class="ri-shopping-bag-3-line"></i>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xxl-3 col-lg-6">
                <a href="{{ route('users.index') }}" class="text-decoration-none">
                    <div class="a-stat-card d-flex justify-content-between">
                        <div>
                            <div class="a-stat-label">Total Customers</div>
                            <div class="a-stat-value">{{ $totalCustomers }}</div>
                        </div>
                        <div class="a-stat-icon" style="background: var(--a-secondary-soft); color: var(--a-secondary);">
                            <i class="ri-user-add-line"></i>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-sm-6 col-xxl-3 col-lg-6">
                <a href="{{ route('products.index') }}" class="text-decoration-none">
                    <div class="a-stat-card d-flex justify-content-between">
                        <div>
                            <div class="a-stat-label">Total Products</div>
                            <div class="a-stat-value">{{ $totalProducts }}</div>
                        </div>
                        <div class="a-stat-icon" style="background: var(--a-warning-soft); color: var(--a-warning);">
                            <i class="ri-store-3-line"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Order status --}}
        <div class="row g-3 mt-0">
            <div class="col-xxl-3 col-sm-6">
                <div class="a-card h-100">
                    <div class="a-card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Pending Orders</h6>
                            <h3 class="mb-0">{{ $pendingOrders }}</h3>
                        </div>
                        <div class="a-stat-icon" style="background: var(--a-danger-soft); color: var(--a-danger);">
                            <i class="ri-time-line"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6">
                <div class="a-card h-100">
                    <div class="a-card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Confirmed</h6>
                            <h3 class="mb-0">{{ $confirmedOrders }}</h3>
                        </div>
                        <div class="a-stat-icon" style="background: var(--a-info-soft); color: var(--a-info);">
                            <i class="ri-check-double-line"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6">
                <div class="a-card h-100">
                    <div class="a-card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Delivered</h6>
                            <h3 class="mb-0">{{ $shippedOrders }}</h3>
                        </div>
                        <div class="a-stat-icon" style="background: var(--a-success-soft); color: var(--a-success);">
                            <i class="ri-truck-line"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6">
                <div class="a-card h-100">
                    <div class="a-card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Cancelled</h6>
                            <h3 class="mb-0">{{ $cancelledOrders }}</h3>
                        </div>
                        <div class="a-stat-icon" style="background: var(--a-secondary-soft); color: var(--a-secondary);">
                            <i class="ri-close-circle-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sales by category + top customers --}}
        <div class="row g-3 mt-0">
            <div class="col-xxl-6">
                <div class="a-card h-100">
                    <div class="a-card-head">
                        <h5>Sales by Category</h5>
                    </div>
                    <div class="a-card-body">
                        @if ($categorySales->isNotEmpty())
                            @php $maxCat = $categorySales->max('revenue'); @endphp
                            @foreach ($categorySales as $cat)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>{{ $cat->category }}</span>
                                        <strong>৳ {{ number_format($cat->revenue, 0) }}</strong>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar"
                                            style="width: {{ $maxCat > 0 ? round(($cat->revenue / $maxCat) * 100) : 0 }}%; background: var(--a-primary);"></div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="a-empty">
                                <div class="a-empty-icon"><i class="ri-pie-chart-line"></i></div>
                                <h5>No sales data</h5>
                                <p>No sales data available for this period.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-xxl-6">
                <div class="a-card h-100">
                    <div class="a-card-head">
                        <h5>Top Customers</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table all-package theme-table">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Orders</th>
                                    <th>Spent</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($topCustomers as $customer)
                                    <tr>
                                        <td>
                                            <div class="a-cell-main">{{ $customer->name }}</div>
                                            <small class="a-cell-sub">{{ $customer->phone }}</small>
                                        </td>
                                        <td>{{ $customer->orders_count }}</td>
                                        <td>৳ {{ number_format($customer->spent, 0) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">
                                            <div class="a-empty py-4">
                                                <h5>No customer data yet.</h5>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Most saleable + top by revenue --}}
        <div class="row g-3 mt-0">
            <div class="col-xxl-6">
                <div class="a-card">
                    <div class="a-card-head">
                        <h5>Most Saleable Products</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table all-package theme-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Units Sold</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($topByUnits as $index => $product)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="a-cell-main">{{ $product->name }}</td>
                                        <td>{{ $product->units }}</td>
                                        <td>৳ {{ number_format($product->revenue, 0) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">
                                            <div class="a-empty py-4">
                                                <h5>No products sold yet</h5>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-xxl-6">
                <div class="a-card">
                    <div class="a-card-head">
                        <h5>Top Products by Revenue</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table all-package theme-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Units</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($topByRevenue as $index => $product)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="a-cell-main">{{ $product->name }}</td>
                                        <td>{{ $product->units }}</td>
                                        <td>৳ {{ number_format($product->revenue, 0) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">
                                            <div class="a-empty py-4">
                                                <h5>No products sold yet</h5>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent orders + recent customers --}}
        <div class="row g-3 mt-0">
            <div class="col-xxl-8">
                <div class="a-card">
                    <div class="a-card-head">
                        <h5>Recent Orders</h5>
                        <a href="{{ route('orders.index') }}" class="btn btn-theme btn-sm">View All Orders</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table all-package theme-table">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Items</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentOrders as $order)
                                    <tr>
                                        <td>
                                            <a href="{{ route('orders.details', $order->id) }}"
                                                class="text-decoration-none fw-semibold">{{ $order->order_id }}</a>
                                        </td>
                                        <td>{{ $order->name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}</td>
                                        <td>{{ $order->items_count }}</td>
                                        <td>৳ {{ number_format($order->grand_total, 0) }}</td>
                                        <td>
                                            @if ($order->status == 'pending')
                                                <span class="a-badge a-badge-danger"><span class="dot"></span>Pending</span>
                                            @elseif ($order->status == 'confirm')
                                                <span class="a-badge a-badge-info"><span class="dot"></span>Confirmed</span>
                                            @elseif ($order->status == 'shipped')
                                                <span class="a-badge a-badge-success"><span class="dot"></span>Delivered</span>
                                            @elseif ($order->status == 'cancell')
                                                <span class="a-badge a-badge-secondary"><span class="dot"></span>Cancelled</span>
                                            @else
                                                <span class="a-badge a-badge-neutral"><span class="dot"></span>{{ $order->status }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <div class="a-empty py-4">
                                                <h5>No orders found</h5>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-xxl-4">
                <div class="a-card h-100">
                    <div class="a-card-head">
                        <h5>Recent Customers</h5>
                        <a href="{{ route('users.index') }}" class="btn btn-theme btn-sm">View All</a>
                    </div>
                    <div class="a-card-body">
                        <ul class="list-group list-group-flush">
                            @forelse ($recentCustomers as $customer)
                                <li class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="a-cell-main">{{ $customer->name }}</div>
                                            <small class="a-cell-sub">{{ $customer->email ?? $customer->phone }}</small>
                                        </div>
                                        <div class="text-end">
                                            <div><small class="text-muted">Orders</small> {{ $customer->valid_orders }}</div>
                                            <div><small class="text-muted">Spent</small> ৳
                                                {{ number_format($customer->total_spent ?? 0, 0) }}</div>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item px-0">
                                    <div class="a-empty py-4">
                                        <h5>No customers yet</h5>
                                    </div>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Inventory + low stock --}}
        <div class="row g-3 mt-0">
            <div class="col-xxl-4 col-md-6">
                <div class="a-card h-100">
                    <div class="a-card-body">
                        <h5 class="mb-3">Inventory Overview</h5>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="border rounded-3 p-3 text-center" style="border-color: var(--a-border) !important;">
                                    <h3 class="mb-1">{{ $inStockVariants }}</h3>
                                    <small class="text-muted">In Stock</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded-3 p-3 text-center" style="border-color: var(--a-border) !important;">
                                    <h3 class="mb-1 text-warning">{{ $lowStockVariants }}</h3>
                                    <small class="text-muted">Low Stock</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded-3 p-3 text-center" style="border-color: var(--a-border) !important;">
                                    <h3 class="mb-1 text-danger">{{ $outOfStockVariants }}</h3>
                                    <small class="text-muted">Out of Stock</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded-3 p-3 text-center" style="border-color: var(--a-border) !important;">
                                    <h3 class="mb-1">{{ $freeDeliveryItems }}</h3>
                                    <small class="text-muted">Free Delivery</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-8 col-md-6">
                <div class="a-card h-100">
                    <div class="a-card-head">
                        <h5>Low Stock Products</h5>
                        <a href="{{ route('products.index') }}" class="btn btn-theme btn-sm">View Inventory</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table all-package theme-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($lowStockProducts as $variant)
                                    <tr>
                                        <td class="a-cell-main">{{ $variant->product->name ?? 'Product' }}</td>
                                        <td>{{ $variant->sku }}</td>
                                        <td>{{ $variant->qty }}</td>
                                        <td>
                                            @if ($variant->qty <= 2)
                                                <span class="a-badge a-badge-danger"><span class="dot"></span>Critical</span>
                                            @else
                                                <span class="a-badge a-badge-warning"><span class="dot"></span>Low Stock</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">
                                            <div class="a-empty py-4">
                                                <h5>No low-stock products</h5>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick actions --}}
        <div class="row mt-0">
            <div class="col-12">
                <div class="a-card">
                    <div class="a-card-body d-flex flex-wrap gap-2">
                        <a href="{{ route('products.create') }}" class="btn btn-theme"><i
                                data-feather="plus-square"></i> Add Product</a>
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">View Orders</a>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Manage Customers</a>
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Categories</a>
                        <a href="{{ route('coupon.index') }}" class="btn btn-outline-secondary">Coupons</a>
                        <a href="{{ route('purchase.index') }}" class="btn btn-outline-secondary">Inventory</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
