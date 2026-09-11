@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                    <li class="is-active">Sales Report</li>
                </ul>
                <h4 class="a-page-title">Sales Report</h4>
                <p class="a-page-desc">Revenue, discounts and shipping · {{ $rangeLabel }}</p>
            </div>
        </div>

        @include('admin.reports.partials.filter', [
            'route' => route('sales.index'),
            'reportTitle' => 'Sales Report',
            'exportReport' => 'sales',
            'selects' => [
                ['name' => 'status', 'label' => 'Order Status', 'options' => ['' => 'All'] + $statuses],
                ['name' => 'payment', 'label' => 'Payment', 'options' => ['' => 'All'] + $payments],
                ['name' => 'group', 'label' => 'Chart Group', 'options' => ['day' => 'Daily', 'week' => 'Weekly', 'month' => 'Monthly']],
            ],
        ])

        @include('admin.reports.partials.kpi', [
            'cards' => [
                [
                    'label' => 'Total Orders',
                    'value' => number_format($summary['orders']),
                    'icon' => 'ri-shopping-bag-3-line',
                    'color' => 'info',
                    'sub' => $growth['orders'] === null ? null : (($growth['orders'] >= 0 ? '↑ ' : '↓ ') . abs($growth['orders']) . '% vs prev'),
                    'delta' => $growth['orders'] === null ? 'flat' : ($growth['orders'] >= 0 ? 'up' : 'down'),
                ],
                [
                    'label' => 'Gross Sales',
                    'value' => '৳ ' . number_format($summary['gross'], 2),
                    'icon' => 'ri-money-dollar-circle-line',
                    'color' => 'primary',
                    'sub' => $growth['gross'] === null ? null : (($growth['gross'] >= 0 ? '↑ ' : '↓ ') . abs($growth['gross']) . '% vs prev'),
                    'delta' => $growth['gross'] === null ? 'flat' : ($growth['gross'] >= 0 ? 'up' : 'down'),
                ],
                [
                    'label' => 'Discount',
                    'value' => '৳ ' . number_format($summary['discount'], 2),
                    'icon' => 'ri-coupon-3-line',
                    'color' => 'warning',
                ],
                [
                    'label' => 'Shipping',
                    'value' => '৳ ' . number_format($summary['shipping'], 2),
                    'icon' => 'ri-truck-line',
                    'color' => 'secondary',
                ],
                [
                    'label' => 'Net Sales',
                    'value' => '৳ ' . number_format($summary['net'], 2),
                    'icon' => 'ri-line-chart-line',
                    'color' => 'success',
                    'sub' => $growth['net'] === null ? 'Gross − Discount' : (($growth['net'] >= 0 ? '↑ ' : '↓ ') . abs($growth['net']) . '% vs prev'),
                    'delta' => $growth['net'] === null ? 'flat' : ($growth['net'] >= 0 ? 'up' : 'down'),
                ],
                [
                    'label' => 'Gross Profit',
                    'value' => '৳ ' . number_format($summary['gross_profit'], 2),
                    'icon' => 'ri-funds-line',
                    'color' => 'success',
                    'sub' => $summary['margin'] . '% margin',
                    'delta' => 'flat',
                    'hint' => 'Net Sales − COGS (variant purchase price).',
                ],
                [
                    'label' => 'Avg Order Value',
                    'value' => '৳ ' . number_format($summary['aov'], 2),
                    'icon' => 'ri-calculator-line',
                    'color' => 'info',
                ],
                [
                    'label' => 'Cancelled (excluded)',
                    'value' => number_format($summary['cancelled_orders']),
                    'icon' => 'ri-close-circle-line',
                    'color' => 'danger',
                    'sub' => '৳ ' . number_format($summary['cancelled_amount'], 0) . ' value',
                    'delta' => 'flat',
                    'hint' => 'Cancelled orders are excluded from all sales figures above.',
                ],
            ],
        ])

        <div class="a-card mb-3">
            <div class="a-card-head">
                <h5>Sales Trend ({{ ucfirst($group) }})</h5>
            </div>
            <div class="a-card-body">
                @if ($trend->isEmpty())
                    <div class="a-empty">
                        <div class="a-empty-icon"><i class="ri-line-chart-line"></i></div>
                        <h5>No data found</h5>
                        <p>There is no data matching the selected filters. Try changing the date range or filters.</p>
                    </div>
                @else
                    <div id="salesChart" class="a-chart"></div>
                @endif
            </div>
        </div>

        <div class="a-card">
            <div class="a-card-head">
                <h5>Daily Breakdown</h5>
            </div>
            <div class="table-responsive">
                <table class="table all-package theme-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th class="a-table-num">Orders</th>
                            <th class="a-table-num">Gross Sales</th>
                            <th class="a-table-num">Discount</th>
                            <th class="a-table-num">Shipping</th>
                            <th class="a-table-num">Net Sales</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            @php
                                $rowNet = (float) $row->gross - (float) $row->discount;
                            @endphp
                            <tr>
                                <td class="a-cell-main">{{ $row->period }}</td>
                                <td class="a-table-num">{{ $row->orders }}</td>
                                <td class="a-table-num">৳ {{ number_format($row->gross, 2) }}</td>
                                <td class="a-table-num">৳ {{ number_format($row->discount, 2) }}</td>
                                <td class="a-table-num">৳ {{ number_format($row->shipping, 2) }}</td>
                                <td class="a-table-num a-cell-main">৳ {{ number_format($rowNet, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="a-empty">
                                        <div class="a-empty-icon"><i class="ri-inbox-line"></i></div>
                                        <h5>No data found</h5>
                                        <p>There is no data matching the selected filters.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($rows->count())
                        <tfoot>
                            <tr>
                                <th>Total</th>
                                <th class="a-table-num">{{ number_format($summary['orders']) }}</th>
                                <th class="a-table-num">৳ {{ number_format($summary['gross'], 2) }}</th>
                                <th class="a-table-num">৳ {{ number_format($summary['discount'], 2) }}</th>
                                <th class="a-table-num">৳ {{ number_format($summary['shipping'], 2) }}</th>
                                <th class="a-table-num">৳ {{ number_format($summary['net'], 2) }}</th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
@endsection

@section('customJs')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const el = document.querySelector('#salesChart');
            if (!el || typeof ApexCharts === 'undefined') return;

            new ApexCharts(el, {
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
                    data: @json($trend->map(fn($r) => round((float) $r->gross - (float) $r->discount, 2)))
                }, {
                    name: 'Gross Sales',
                    data: @json($trend->pluck('gross'))
                }, {
                    name: 'Orders',
                    data: @json($trend->pluck('orders'))
                }],
                colors: ['#0da487', '#2563eb', '#d97706'],
                stroke: {
                    curve: 'smooth',
                    width: 2.5
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        opacityFrom: 0.2,
                        opacityTo: 0.02
                    }
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: @json($trend->pluck('period')),
                    labels: {
                        rotate: -45
                    }
                },
                yaxis: [{
                    title: {
                        text: 'Sales'
                    }
                }, {
                    title: {
                        text: 'Sales'
                    },
                    show: false
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
        });
    </script>
@endsection
