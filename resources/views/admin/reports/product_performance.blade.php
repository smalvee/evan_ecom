@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                    <li class="is-active">Product Performance</li>
                </ul>
                <h4 class="a-page-title">Product Performance</h4>
                <p class="a-page-desc">Best &amp; worst sellers, category and brand performance ·
                    {{ $rangeLabel }}</p>
            </div>
        </div>

        @include('admin.reports.partials.filter', [
            'route' => route('admin.reports.product_performance'),
            'reportTitle' => 'Product Performance',
            'exportReport' => 'product_sales',
            'selects' => [
                ['name' => 'status', 'label' => 'Order Status', 'options' => ['' => 'All'] + $statuses],
                ['name' => 'category', 'label' => 'Category', 'options' => ['' => 'All'] + $categories->pluck('name', 'id')->toArray()],
                ['name' => 'brand', 'label' => 'Brand', 'options' => ['' => 'All'] + $brands->pluck('name', 'id')->toArray()],
            ],
        ])

        <div class="row g-3 mb-3">
            <div class="col-xxl-6">
                <div class="a-card h-100">
                    <div class="a-card-head">
                        <h5>Best Selling Products (Top 10 by Units)</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table all-package theme-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th class="a-table-num">Units</th>
                                    <th class="a-table-num">Revenue</th>
                                    <th class="a-table-num">Profit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($best as $i => $p)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td class="a-cell-main">{{ $p->product ?? '—' }}</td>
                                        <td class="a-table-num">{{ number_format($p->units) }}</td>
                                        <td class="a-table-num">৳ {{ number_format($p->revenue, 0) }}</td>
                                        <td class="a-table-num {{ $p->profit >= 0 ? 'text-success' : 'text-danger' }}">৳
                                            {{ number_format($p->profit, 0) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <div class="a-empty py-4">
                                                <h5>No data found</h5>
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
                <div class="a-card h-100">
                    <div class="a-card-head">
                        <h5>Slow / Worst Selling Products (Lowest Units)</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table all-package theme-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th class="a-table-num">Units</th>
                                    <th class="a-table-num">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($worst as $i => $p)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td class="a-cell-main">{{ $p->product ?? '—' }}</td>
                                        <td class="a-table-num">{{ number_format($p->units) }}</td>
                                        <td class="a-table-num">৳ {{ number_format($p->revenue, 0) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">
                                            <div class="a-empty py-4">
                                                <h5>No data found</h5>
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

        <div class="row g-3 mb-3">
            <div class="col-xxl-6">
                <div class="a-card h-100">
                    <div class="a-card-head">
                        <h5>Category Performance</h5>
                    </div>
                    <div class="a-card-body">
                        @if ($categorySales->isEmpty())
                            <div class="a-empty">
                                <div class="a-empty-icon"><i class="ri-pie-chart-line"></i></div>
                                <h5>No data found</h5>
                            </div>
                        @else
                            <div id="categoryChart" class="a-chart"></div>
                        @endif
                    </div>
                    <div class="table-responsive">
                        <table class="table all-package theme-table">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th class="a-table-num">Units</th>
                                    <th class="a-table-num">Revenue</th>
                                    <th class="a-table-num">Profit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($categorySales as $c)
                                    @php $cProfit = (float) $c->revenue - (float) $c->cost; @endphp
                                    <tr>
                                        <td class="a-cell-main">{{ $c->category }}</td>
                                        <td class="a-table-num">{{ number_format($c->units) }}</td>
                                        <td class="a-table-num">৳ {{ number_format($c->revenue, 0) }}</td>
                                        <td class="a-table-num {{ $cProfit >= 0 ? 'text-success' : 'text-danger' }}">৳
                                            {{ number_format($cProfit, 0) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">
                                            <div class="a-empty py-4">
                                                <h5>No data found</h5>
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
                <div class="a-card h-100">
                    <div class="a-card-head">
                        <h5>Brand Performance</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table all-package theme-table">
                            <thead>
                                <tr>
                                    <th>Brand</th>
                                    <th class="a-table-num">Products</th>
                                    <th class="a-table-num">Units</th>
                                    <th class="a-table-num">Revenue</th>
                                    <th class="a-table-num">Profit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($brandSales as $b)
                                    @php $bProfit = (float) $b->revenue - (float) $b->cost; @endphp
                                    <tr>
                                        <td class="a-cell-main">{{ $b->brand }}</td>
                                        <td class="a-table-num">{{ number_format($b->products) }}</td>
                                        <td class="a-table-num">{{ number_format($b->units) }}</td>
                                        <td class="a-table-num">৳ {{ number_format($b->revenue, 0) }}</td>
                                        <td class="a-table-num {{ $bProfit >= 0 ? 'text-success' : 'text-danger' }}">৳
                                            {{ number_format($bProfit, 0) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <div class="a-empty py-4">
                                                <h5>No data found</h5>
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

        <div class="alert alert-secondary mb-0">
            <i class="ri-information-line"></i> Product return rate is not calculated because customer returns are not
            tracked by this application.
        </div>
    </div>
@endsection

@section('customJs')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const el = document.querySelector('#categoryChart');
            if (!el || typeof ApexCharts === 'undefined') return;
            new ApexCharts(el, {
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
                    data: @json($categorySales->pluck('revenue'))
                }],
                xaxis: {
                    categories: @json($categorySales->pluck('category'))
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
        });
    </script>
@endsection
