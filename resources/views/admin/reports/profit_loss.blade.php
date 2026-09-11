@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                    <li class="is-active">Profit &amp; Loss</li>
                </ul>
                <h4 class="a-page-title">Profit &amp; Loss</h4>
                <p class="a-page-desc">Gross profit based on product cost · {{ $rangeLabel }}</p>
            </div>
        </div>

        @include('admin.reports.partials.filter', [
            'route' => route('admin.reports.profit_loss'),
            'reportTitle' => 'Profit & Loss',
            'exportReport' => 'profit_loss',
            'selects' => [
                ['name' => 'status', 'label' => 'Order Status', 'options' => ['' => 'All'] + $statuses],
            ],
        ])

        @include('admin.reports.partials.kpi', [
            'cards' => [
                ['label' => 'Revenue (Net Sales)', 'value' => '৳ ' . number_format($pl['net'], 2), 'icon' => 'ri-money-dollar-circle-line', 'color' => 'primary', 'hint' => 'Gross Sales − Discounts.'],
                ['label' => 'Cost of Goods Sold', 'value' => '৳ ' . number_format($pl['cogs'], 2), 'icon' => 'ri-shopping-cart-2-line', 'color' => 'warning', 'hint' => 'Variant purchase price × quantity sold.'],
                ['label' => 'Gross Profit', 'value' => '৳ ' . number_format($pl['gross_profit'], 2), 'icon' => 'ri-funds-line', 'color' => 'success'],
                ['label' => 'Gross Margin', 'value' => $pl['margin'] . '%', 'icon' => 'ri-percent-line', 'color' => 'info'],
            ],
        ])

        <div class="row g-3 mb-3">
            <div class="col-xxl-7">
                <div class="a-card h-100">
                    <div class="a-card-head">
                        <h5>Profit &amp; Loss Statement</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table all-package theme-table">
                            <tbody>
                                <tr>
                                    <td class="a-cell-main">Gross Sales</td>
                                    <td class="a-table-num">৳ {{ number_format($pl['gross'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Less: Discounts</td>
                                    <td class="a-table-num text-danger">− ৳ {{ number_format($pl['discount'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="a-cell-main">Net Sales</td>
                                    <td class="a-table-num a-cell-main">৳ {{ number_format($pl['net'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Less: Cost of Goods Sold</td>
                                    <td class="a-table-num text-danger">− ৳ {{ number_format($pl['cogs'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="a-cell-main">Gross Profit</td>
                                    <td class="a-table-num a-cell-main text-success">৳
                                        {{ number_format($pl['gross_profit'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Gross Margin</td>
                                    <td class="a-table-num">{{ $pl['margin'] }}%</td>
                                </tr>
                                <tr>
                                    <td>Shipping Collected</td>
                                    <td class="a-table-num">৳ {{ number_format($pl['shipping'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="a-cell-main">Collected Total (Grand Total)</td>
                                    <td class="a-table-num a-cell-main">৳ {{ number_format($pl['grand'], 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-xxl-5">
                <div class="a-card h-100">
                    <div class="a-card-head">
                        <h5>Monthly Trend</h5>
                    </div>
                    <div class="a-card-body">
                        @if ($trend->isEmpty())
                            <div class="a-empty">
                                <div class="a-empty-icon"><i class="ri-line-chart-line"></i></div>
                                <h5>No data found</h5>
                            </div>
                        @else
                            <div id="plChart" class="a-chart"></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-warning mb-0">
            <i class="ri-alert-line"></i>
            <strong>Note:</strong> Net profit is not shown because operating expenses are not tracked by this
            application. Only <strong>Gross Profit</strong> (Net Sales − Cost of Goods Sold) can be calculated from the
            available data.
        </div>
    </div>
@endsection

@section('customJs')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const el = document.querySelector('#plChart');
            if (!el || typeof ApexCharts === 'undefined') return;
            new ApexCharts(el, {
                chart: {
                    type: 'line',
                    height: 300,
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'Inter, sans-serif'
                },
                series: [{
                    name: 'Net Sales',
                    data: @json($trend->map(fn($r) => round((float) $r->gross - (float) $r->discount, 2)))
                }, {
                    name: 'Collected',
                    data: @json($trend->pluck('grand'))
                }],
                colors: ['#0da487', '#2563eb'],
                stroke: {
                    curve: 'smooth',
                    width: 2.5
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: @json($trend->pluck('period'))
                },
                grid: {
                    borderColor: '#e6eaf0'
                },
                legend: {
                    position: 'top'
                }
            }).render();
        });
    </script>
@endsection
