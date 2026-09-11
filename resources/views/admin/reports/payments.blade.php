@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                    <li class="is-active">Payment Report</li>
                </ul>
                <h4 class="a-page-title">Payment Report</h4>
                <p class="a-page-desc">Paid vs unpaid orders · {{ $rangeLabel }}</p>
            </div>
        </div>

        @include('admin.reports.partials.filter', [
            'route' => route('admin.reports.payments'),
            'reportTitle' => 'Payment Report',
            'exportReport' => 'payments',
            'showSearch' => true,
            'searchPlaceholder' => 'Order ID or customer',
            'selects' => [
                ['name' => 'payment', 'label' => 'Payment Status', 'options' => ['' => 'All'] + $payments],
            ],
        ])

        @php
            $totalAmount = $summary['paid']['amount'] + $summary['unpaid']['amount'];
            $collectionRate = $totalAmount > 0 ? round(($summary['paid']['amount'] / $totalAmount) * 100, 1) : 0;
        @endphp

        @include('admin.reports.partials.kpi', [
            'cards' => [
                ['label' => 'Total Paid', 'value' => '৳ ' . number_format($summary['paid']['amount'], 2), 'icon' => 'ri-check-double-line', 'color' => 'success', 'sub' => number_format($summary['paid']['count']) . ' orders', 'delta' => 'flat'],
                ['label' => 'Total Unpaid', 'value' => '৳ ' . number_format($summary['unpaid']['amount'], 2), 'icon' => 'ri-error-warning-line', 'color' => 'warning', 'sub' => number_format($summary['unpaid']['count']) . ' orders', 'delta' => 'flat'],
                ['label' => 'Collection Rate', 'value' => $collectionRate . '%', 'icon' => 'ri-percent-line', 'color' => 'info'],
                ['label' => 'Total Billed', 'value' => '৳ ' . number_format($totalAmount, 2), 'icon' => 'ri-money-dollar-circle-line', 'color' => 'primary'],
            ],
        ])

        <div class="row g-3 mb-3">
            <div class="col-xxl-4">
                <div class="a-card h-100">
                    <div class="a-card-head">
                        <h5>Payment Split</h5>
                    </div>
                    <div class="a-card-body">
                        @if ($totalAmount > 0)
                            <div id="paymentChart" class="a-chart"></div>
                        @else
                            <div class="a-empty">
                                <div class="a-empty-icon"><i class="ri-pie-chart-line"></i></div>
                                <h5>No data found</h5>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-xxl-8">
                <div class="a-card h-100">
                    <div class="a-card-head">
                        <h5>Payment Status Summary</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table all-package theme-table">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th class="a-table-num">Orders</th>
                                    <th class="a-table-num">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="a-badge a-badge-success"><span class="dot"></span>Paid</span></td>
                                    <td class="a-table-num">{{ number_format($summary['paid']['count']) }}</td>
                                    <td class="a-table-num">৳ {{ number_format($summary['paid']['amount'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td><span class="a-badge a-badge-warning"><span class="dot"></span>Unpaid</span></td>
                                    <td class="a-table-num">{{ number_format($summary['unpaid']['count']) }}</td>
                                    <td class="a-table-num">৳ {{ number_format($summary['unpaid']['amount'], 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="a-card-body pt-0">
                        <div class="alert alert-secondary mb-0">
                            <i class="ri-information-line"></i> This application stores only a paid/unpaid flag per order.
                            Payment <em>methods</em> (COD, bKash, bank, etc.) are not recorded, so they are not reported.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="a-card">
            <div class="a-card-head">
                <h5>Payment Records</h5>
            </div>
            <div class="table-responsive">
                <table class="table all-package theme-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th class="a-table-num">Amount</th>
                            <th>Payment Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('orders.details', $order->id) }}"
                                        class="a-cell-main text-decoration-none">{{ $order->order_id }}</a>
                                </td>
                                <td>{{ $order->name }}</td>
                                <td class="a-table-num">৳ {{ number_format($order->grand_total, 2) }}</td>
                                <td>
                                    @if ($order->payment_status)
                                        <span class="a-badge a-badge-success"><span class="dot"></span>Paid</span>
                                    @else
                                        <span class="a-badge a-badge-warning"><span class="dot"></span>Unpaid</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="a-empty">
                                        <div class="a-empty-icon"><i class="ri-inbox-line"></i></div>
                                        <h5>No data found</h5>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($orders->hasPages())
                <div class="a-card-footer">{{ $orders->links() }}</div>
            @endif
        </div>
    </div>
@endsection

@section('customJs')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const el = document.querySelector('#paymentChart');
            if (!el || typeof ApexCharts === 'undefined') return;
            new ApexCharts(el, {
                chart: {
                    type: 'donut',
                    height: 300,
                    fontFamily: 'Inter, sans-serif'
                },
                series: [{{ $summary['paid']['amount'] }}, {{ $summary['unpaid']['amount'] }}],
                labels: ['Paid', 'Unpaid'],
                colors: ['#16a34a', '#d97706'],
                legend: {
                    position: 'bottom'
                },
                dataLabels: {
                    enabled: false
                }
            }).render();
        });
    </script>
@endsection
