@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        {{-- Header + date filter --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
            <h4 class="mb-0">Dashboard</h4>
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

        {{-- KPI cards --}}
        <div class="row">
            <div class="col-sm-6 col-xxl-3 col-lg-6">
                <div class="main-tiles border-5 border-0 card-hover card o-hidden">
                    <div class="custome-1-bg b-r-4 card-body">
                        <div class="media align-items-center static-top-widget">
                            <div class="media-body p-0">
                                <span class="m-0">
                                    Total Revenue
                                    <i class="ri-information-line text-muted" style="cursor: help;"
                                        data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true"
                                        title="<strong>How Total Revenue is calculated</strong><br>Sum of <code>grand_total</code> for all orders that are <b>not cancelled</b> within the selected date range."></i>
                                </span>
                                <h4 class="mb-0">৳ {{ number_format($totalRevenue, 2) }}</h4>
                                @if ($revenueChange !== null)
                                    <small class="{{ $revenueChange >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $revenueChange >= 0 ? '↑' : '↓' }} {{ abs($revenueChange) }}% vs previous
                                    </small>
                                @else
                                    <small class="text-muted">No previous-period data</small>
                                @endif
                            </div>
                            <div class="align-self-center text-center">
                                <i class="ri-money-dollar-circle-line"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xxl-3 col-lg-6">
                <div class="main-tiles border-5 card-hover border-0 card o-hidden">
                    <div class="custome-2-bg b-r-4 card-body">
                        <div class="media static-top-widget">
                            <div class="media-body p-0">
                                <span class="m-0">Total Orders</span>
                                <h4 class="mb-0">{{ $totalOrders }}</h4>
                                @if ($ordersChange !== null)
                                    <small class="{{ $ordersChange >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $ordersChange >= 0 ? '↑' : '↓' }} {{ abs($ordersChange) }}% vs previous
                                    </small>
                                @else
                                    <small class="text-muted">No previous-period data</small>
                                @endif
                            </div>
                            <div class="align-self-center text-center">
                                <i class="ri-shopping-bag-3-line"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xxl-3 col-lg-6">
                <a href="{{ route('users.index') }}" class="text-decoration-none">
                    <div class="main-tiles border-5 card-hover border-0 card o-hidden">
                        <div class="custome-4-bg b-r-4 card-body">
                            <div class="media static-top-widget">
                                <div class="media-body p-0">
                                    <span class="m-0">Total Customers</span>
                                    <h4 class="mb-0">{{ $totalCustomers }}</h4>
                                </div>
                                <div class="align-self-center text-center">
                                    <i class="ri-user-add-line"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-sm-6 col-xxl-3 col-lg-6">
                <a href="{{ route('products.index') }}" class="text-decoration-none">
                    <div class="main-tiles border-5 card-hover border-0 card o-hidden">
                        <div class="custome-3-bg b-r-4 card-body">
                            <div class="media static-top-widget">
                                <div class="media-body p-0">
                                    <span class="m-0">Total Products</span>
                                    <h4 class="mb-0">{{ $totalProducts }}</h4>
                                </div>
                                <div class="align-self-center text-center">
                                    <i class="ri-store-3-line"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Order status --}}
        <div class="row">
            <div class="col-xxl-3 col-sm-6">
                <div class="card card-hover o-hidden border-0">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Pending Orders</h6>
                            <h3 class="mb-0">{{ $pendingOrders }}</h3>
                        </div>
                        <div class="text-center text-white rounded-3 p-3" style="background:#dc3545;">
                            <i class="ri-time-line fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6">
                <div class="card card-hover o-hidden border-0">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Confirmed</h6>
                            <h3 class="mb-0">{{ $confirmedOrders }}</h3>
                        </div>
                        <div class="text-center text-white rounded-3 p-3" style="background:#0dcaf0;">
                            <i class="ri-check-double-line fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6">
                <div class="card card-hover o-hidden border-0">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Delivered</h6>
                            <h3 class="mb-0">{{ $shippedOrders }}</h3>
                        </div>
                        <div class="text-center text-white rounded-3 p-3" style="background:#198754;">
                            <i class="ri-truck-line fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-sm-6">
                <div class="card card-hover o-hidden border-0">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">Cancelled</h6>
                            <h3 class="mb-0">{{ $cancelledOrders }}</h3>
                        </div>
                        <div class="text-center text-white rounded-3 p-3" style="background:#6c757d;">
                            <i class="ri-close-circle-line fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sales by category + top customers --}}
        <div class="row">
            <div class="col-xxl-6">
                <div class="card card-hover h-100">
                    <div class="card-body">
                        <div class="title-header option-title">
                            <h5>Sales by Category</h5>
                        </div>
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
                                            style="width: {{ $maxCat > 0 ? round(($cat->revenue / $maxCat) * 100) : 0 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">No sales data available for this period.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-xxl-6">
                <div class="card card-hover h-100">
                    <div class="card-body">
                        <div class="title-header option-title">
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
                                                <div class="fw-semibold">{{ $customer->name }}</div>
                                                <small class="text-muted">{{ $customer->phone }}</small>
                                            </td>
                                            <td>{{ $customer->orders_count }}</td>
                                            <td>৳ {{ number_format($customer->spent, 0) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-muted">No customer data yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Most saleable + top by revenue --}}
        <div class="row">
            <div class="col-xxl-6">
                <div class="card card-hover">
                    <div class="card-body">
                        <div class="title-header option-title">
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
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->units }}</td>
                                            <td>৳ {{ number_format($product->revenue, 0) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-muted">No products have been sold yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-6">
                <div class="card card-hover">
                    <div class="card-body">
                        <div class="title-header option-title">
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
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->units }}</td>
                                            <td>৳ {{ number_format($product->revenue, 0) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-muted">No products have been sold yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent orders + recent customers --}}
        <div class="row">
            <div class="col-xxl-8">
                <div class="card card-table card-hover">
                    <div class="card-body">
                        <div class="title-header option-title">
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
                                                    class="text-decoration-none">{{ $order->order_id }}</a>
                                            </td>
                                            <td>{{ $order->name }}</td>
                                            <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}</td>
                                            <td>{{ $order->items_count }}</td>
                                            <td>৳ {{ number_format($order->grand_total, 0) }}</td>
                                            <td>
                                                @if ($order->status == 'pending')
                                                    <span class="badge bg-danger">Pending</span>
                                                @elseif ($order->status == 'confirm')
                                                    <span class="badge bg-info text-dark">Confirmed</span>
                                                @elseif ($order->status == 'shipped')
                                                    <span class="badge bg-success">Delivered</span>
                                                @elseif ($order->status == 'cancell')
                                                    <span class="badge bg-secondary">Cancelled</span>
                                                @else
                                                    <span class="badge bg-light text-dark">{{ $order->status }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-muted">No orders found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-4">
                <div class="card card-table card-hover">
                    <div class="card-body">
                        <div class="title-header option-title">
                            <h5>Recent Customers</h5>
                            <a href="{{ route('users.index') }}" class="btn btn-theme btn-sm">View All</a>
                        </div>
                        <ul class="list-group list-group-flush">
                            @forelse ($recentCustomers as $customer)
                                <li class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-semibold">{{ $customer->name }}</div>
                                            <small class="text-muted">{{ $customer->email ?? $customer->phone }}</small>
                                        </div>
                                        <div class="text-end">
                                            <div><small class="text-muted">Orders</small> {{ $customer->valid_orders }}
                                            </div>
                                            <div><small class="text-muted">Spent</small> ৳
                                                {{ number_format($customer->total_spent ?? 0, 0) }}</div>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item px-0 text-muted">No customers yet.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Inventory + low stock --}}
        <div class="row">
            <div class="col-xxl-4 col-md-6">
                <div class="card card-hover h-100">
                    <div class="card-body">
                        <h5 class="mb-3">Inventory Overview</h5>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="border rounded-3 p-3 text-center">
                                    <h3 class="mb-1">{{ $inStockVariants }}</h3>
                                    <small class="text-muted">In Stock</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded-3 p-3 text-center">
                                    <h3 class="mb-1 text-warning">{{ $lowStockVariants }}</h3>
                                    <small class="text-muted">Low Stock</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded-3 p-3 text-center">
                                    <h3 class="mb-1 text-danger">{{ $outOfStockVariants }}</h3>
                                    <small class="text-muted">Out of Stock</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded-3 p-3 text-center">
                                    <h3 class="mb-1">{{ $freeDeliveryItems }}</h3>
                                    <small class="text-muted">Free Delivery Items</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-8 col-md-6">
                <div class="card card-table card-hover h-100">
                    <div class="card-body">
                        <div class="title-header option-title">
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
                                            <td>{{ $variant->product->name ?? 'Product' }}</td>
                                            <td>{{ $variant->sku }}</td>
                                            <td>{{ $variant->qty }}</td>
                                            <td>
                                                @if ($variant->qty <= 2)
                                                    <span class="badge bg-danger">Critical</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Low Stock</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-muted">No low-stock products.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick actions --}}
        <div class="row">
            <div class="col-12">
                <div class="card card-hover">
                    <div class="card-body d-flex flex-wrap gap-2">
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
