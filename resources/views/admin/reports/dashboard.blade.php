@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="is-active">Reports</li>
                </ul>
                <h4 class="a-page-title">Reports Dashboard</h4>
                <p class="a-page-desc">Business overview · {{ $rangeLabel }}</p>
            </div>
        </div>

        @include('admin.reports.partials.filter', [
            'route' => route('admin.reports.index'),
            'reportTitle' => 'Reports Dashboard',
            'exportReport' => null,
        ])

        @include('admin.reports.partials.kpi', [
            'cards' => [
                [
                    'label' => 'Net Revenue',
                    'value' => '৳ ' . number_format($sales['net'], 0),
                    'icon' => 'ri-money-dollar-circle-line',
                    'color' => 'primary',
                    'sub' => $growth['revenue'] === null ? null : (($growth['revenue'] >= 0 ? '↑ ' : '↓ ') . abs($growth['revenue']) . '% vs prev'),
                    'delta' => $growth['revenue'] === null ? 'flat' : ($growth['revenue'] >= 0 ? 'up' : 'down'),
                    'hint' => 'Gross Sales − Discounts for non-cancelled orders.',
                ],
                [
                    'label' => 'Orders',
                    'value' => number_format($sales['orders']),
                    'icon' => 'ri-shopping-bag-3-line',
                    'color' => 'info',
                    'sub' => $growth['orders'] === null ? null : (($growth['orders'] >= 0 ? '↑ ' : '↓ ') . abs($growth['orders']) . '% vs prev'),
                    'delta' => $growth['orders'] === null ? 'flat' : ($growth['orders'] >= 0 ? 'up' : 'down'),
                ],
                [
                    'label' => 'Gross Profit',
                    'value' => '৳ ' . number_format($sales['gross_profit'], 0),
                    'icon' => 'ri-line-chart-line',
                    'color' => 'success',
                    'sub' => $sales['margin'] . '% margin',
                    'delta' => 'flat',
                    'hint' => 'Net Sales − Cost of Goods Sold (variant purchase price).',
                ],
                [
                    'label' => 'Customers',
                    'value' => number_format($customers['total']),
                    'icon' => 'ri-user-3-line',
                    'color' => 'warning',
                    'sub' => $customers['active'] . ' active in period',
                    'delta' => 'flat',
                ],
            ],
        ])

        <div class="row g-3 mb-3">
            <div class="col-xxl-8">
                <div class="a-card h-100">
                    <div class="a-card-head">
                        <h5>Sales Overview</h5>
                        <span class="a-badge a-badge-neutral"><span class="dot"></span>Net sales &amp; orders</span>
                    </div>
                    <div class="a-card-body">
                        <div id="salesTrendChart" class="a-chart"></div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-4">
                <div class="a-card h-100">
                    <div class="a-card-head">
                        <h5>Order Status</h5>
                    </div>
                    <div class="a-card-body">
                        <div id="orderStatusChart" class="a-chart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-xxl-6">
                <div class="a-card h-100">
                    <div class="a-card-head">
                        <h5>Category Performance</h5>
                    </div>
                    <div class="a-card-body">
                        @if ($categories->isEmpty())
                            <div class="a-empty">
                                <div class="a-empty-icon"><i class="ri-pie-chart-line"></i></div>
                                <h5>No data found</h5>
                                <p>There is no category sales data for the selected range.</p>
                            </div>
                        @else
                            <div id="categoryChart" class="a-chart"></div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-xxl-6">
                <div class="a-card h-100">
                    <div class="a-card-head">
                        <h5>Top Products</h5>
                    </div>
                    <div class="a-card-body">
                        @if ($top_products->isEmpty())
                            <div class="a-empty">
                                <div class="a-empty-icon"><i class="ri-bar-chart-line"></i></div>
                                <h5>No data found</h5>
                                <p>No products were sold in the selected range.</p>
                            </div>
                        @else
                            <div id="topProductsChart" class="a-chart"></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xxl-8">
                <div class="a-card h-100">
                    <div class="a-card-head">
                        <h5>Inventory Alerts</h5>
                        <a href="{{ route('admin.reports.inventory', ['stock' => 'low']) }}"
                            class="btn btn-outline-secondary btn-sm">View Inventory</a>
                    </div>
                    <div class="a-card-body">
                        <div class="row g-3">
                            <div class="col-6 col-md-3">
                                <div class="border rounded-3 p-3 text-center"
                                    style="border-color: var(--a-border) !important;">
                                    <h3 class="mb-1">{{ number_format($inventory['variants']) }}</h3>
                                    <small class="text-muted">Total Variants</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border rounded-3 p-3 text-center"
                                    style="border-color: var(--a-border) !important;">
                                    <h3 class="mb-1">{{ number_format($inventory['units']) }}</h3>
                                    <small class="text-muted">Stock Units</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border rounded-3 p-3 text-center"
                                    style="border-color: var(--a-border) !important;">
                                    <h3 class="mb-1 text-warning">{{ number_format($inventory['low_stock']) }}</h3>
                                    <small class="text-muted">Low Stock</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border rounded-3 p-3 text-center"
                                    style="border-color: var(--a-border) !important;">
                                    <h3 class="mb-1 text-danger">{{ number_format($inventory['out_of_stock']) }}</h3>
                                    <small class="text-muted">Out of Stock</small>
                                </div>
                            </div>
                        </div>
                        <p class="text-muted mt-3 mb-0">
                            Stock value (at cost): <strong>৳ {{ number_format($inventory['stock_value'], 0) }}</strong>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-xxl-4">
                <div class="a-card h-100">
                    <div class="a-card-head">
                        <h5>Quick Links</h5>
                    </div>
                    <div class="a-card-body d-flex flex-column gap-2">
                        <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary justify-content-start"><i
                                class="ri-line-chart-line"></i> Sales Report</a>
                        <a href="{{ route('admin.reports.profit_loss') }}"
                            class="btn btn-outline-secondary justify-content-start"><i class="ri-funds-line"></i> Profit
                            &amp; Loss</a>
                        <a href="{{ route('admin.reports.orders') }}"
                            class="btn btn-outline-secondary justify-content-start"><i class="ri-shopping-bag-3-line"></i>
                            Order Report</a>
                        <a href="{{ route('admin.reports.purchases') }}"
                            class="btn btn-outline-secondary justify-content-start"><i
                                class="ri-shopping-cart-2-line"></i> Purchase Report</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customJs')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof ApexCharts === 'undefined') return;

            const periods = @json($trend->pluck('period'));
            const netSeries = @json($trend->map(fn($r) => round((float) $r->gross - (float) $r->discount, 2)));
            const orderSeries = @json($trend->pluck('orders'));

            new ApexCharts(document.querySelector('#salesTrendChart'), {
                chart: {
                    type: 'area',
                    height: 320,
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'Inter, sans-serif'
                },
                series: [{
                    name: 'Net Sales',
                    data: netSeries
                }, {
                    name: 'Orders',
                    data: orderSeries
                }],
                colors: ['#0da487', '#2563eb'],
                stroke: {
                    curve: 'smooth',
                    width: 2.5
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.25,
                        opacityTo: 0.02
                    }
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: periods,
                    labels: {
                        rotate: -45
                    }
                },
                yaxis: [{
                    title: {
                        text: 'Net Sales'
                    }
                }, {
                    opposite: true,
                    title: {
                        text: 'Orders'
                    }
                }],
                grid: {
                    borderColor: '#e6eaf0'
                },
                legend: {
                    position: 'top'
                }
            }).render();

            const statusData = @json(collect($orders['by_status'])->map(fn($v) => $v['count'])->values());
            const statusLabels = @json(collect($orders['by_status'])->keys()->map(fn($s) => ucfirst($s))->values());
            if (statusData.length) {
                new ApexCharts(document.querySelector('#orderStatusChart'), {
                    chart: {
                        type: 'donut',
                        height: 320,
                        fontFamily: 'Inter, sans-serif'
                    },
                    series: statusData,
                    labels: statusLabels,
                    colors: ['#d97706', '#2563eb', '#16a34a', '#64748b'],
                    legend: {
                        position: 'bottom'
                    },
                    dataLabels: {
                        enabled: false
                    }
                }).render();
            } else {
                document.querySelector('#orderStatusChart').innerHTML =
                    '<div class="a-empty py-4"><h5>No orders found</h5></div>';
            }

            const catEl = document.querySelector('#categoryChart');
            if (catEl) {
                new ApexCharts(catEl, {
                    chart: {
                        type: 'bar',
                        height: 300,
                        toolbar: {
                            show: false
                        },
                        fontFamily: 'Inter, sans-serif'
                    },
                    series: [{
                        name: 'Revenue',
                        data: @json($categories->pluck('revenue'))
                    }],
                    xaxis: {
                        categories: @json($categories->pluck('category'))
                    },
                    colors: ['#0da487'],
                    plotOptions: {
                        bar: {
                            borderRadius: 6,
                            columnWidth: '50%'
                        }
                    },
                    dataLabels: {
                        enabled: false
                    }
                }).render();
            }

            const topEl = document.querySelector('#topProductsChart');
            if (topEl) {
                new ApexCharts(topEl, {
                    chart: {
                        type: 'bar',
                        height: 300,
                        toolbar: {
                            show: false
                        },
                        fontFamily: 'Inter, sans-serif'
                    },
                    series: [{
                        name: 'Revenue',
                        data: @json($top_products->pluck('revenue'))
                    }],
                    xaxis: {
                        categories: @json($top_products->pluck('product'))
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            borderRadius: 6
                        }
                    },
                    colors: ['#2563eb'],
                    dataLabels: {
                        enabled: false
                    }
                }).render();
            }
        });
    </script>
@endsection
