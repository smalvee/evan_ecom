@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                    <li class="is-active">Shipping Report</li>
                </ul>
                <h4 class="a-page-title">Shipping Report</h4>
                <p class="a-page-desc">Delivery status and shipping charges · {{ $rangeLabel }}</p>
            </div>
        </div>

        @include('admin.reports.partials.filter', [
            'route' => route('admin.reports.shipping'),
            'reportTitle' => 'Shipping Report',
            'exportReport' => 'shipping',
        ])

        @php
            $bs = $summary['by_status'];
            $delivered = $bs['shipped']['count'] ?? 0;
            $pending = ($bs['pending']['count'] ?? 0) + ($bs['confirm']['count'] ?? 0);
            $cancelled = $bs['cancell']['count'] ?? 0;
        @endphp

        @include('admin.reports.partials.kpi', [
            'cards' => [
                ['label' => 'Total Shipments', 'value' => number_format($summary['total']), 'icon' => 'ri-truck-line', 'color' => 'primary'],
                ['label' => 'Delivered', 'value' => number_format($delivered), 'icon' => 'ri-check-double-line', 'color' => 'success'],
                ['label' => 'Pending / In Progress', 'value' => number_format($pending), 'icon' => 'ri-time-line', 'color' => 'warning'],
                ['label' => 'Cancelled', 'value' => number_format($cancelled), 'icon' => 'ri-close-circle-line', 'color' => 'danger'],
                ['label' => 'Shipping Collected', 'value' => '৳ ' . number_format($summary['total_charge'], 2), 'icon' => 'ri-money-dollar-circle-line', 'color' => 'info'],
            ],
        ])

        <div class="row g-3">
            <div class="col-xxl-5">
                <div class="a-card h-100">
                    <div class="a-card-head">
                        <h5>Shipments by Status</h5>
                    </div>
                    <div class="a-card-body">
                        @if ($summary['total'] > 0)
                            <div id="shippingChart" class="a-chart"></div>
                        @else
                            <div class="a-empty">
                                <div class="a-empty-icon"><i class="ri-truck-line"></i></div>
                                <h5>No data found</h5>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-xxl-7">
                <div class="a-card h-100">
                    <div class="a-card-head">
                        <h5>Delivery Status Summary</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table all-package theme-table">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th class="a-table-num">Shipments</th>
                                    <th class="a-table-num">Shipping Charge</th>
                                    <th class="a-table-num">Order Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($bs as $status => $s)
                                    <tr>
                                        <td class="a-cell-main">{{ ucfirst($status) }}</td>
                                        <td class="a-table-num">{{ number_format($s['count']) }}</td>
                                        <td class="a-table-num">৳ {{ number_format($s['charge'], 2) }}</td>
                                        <td class="a-table-num">৳ {{ number_format($s['amount'], 2) }}</td>
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
                            @if (count($bs))
                                <tfoot>
                                    <tr>
                                        <th>Total</th>
                                        <th class="a-table-num">{{ number_format($summary['total']) }}</th>
                                        <th class="a-table-num">৳ {{ number_format($summary['total_charge'], 2) }}</th>
                                        <th class="a-table-num"></th>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                    <div class="a-card-body pt-0">
                        <div class="alert alert-secondary mb-0">
                            <i class="ri-information-line"></i> Area/division-wise shipping is not available because orders
                            do not store the district on the order record.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customJs')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const el = document.querySelector('#shippingChart');
            if (!el || typeof ApexCharts === 'undefined') return;
            new ApexCharts(el, {
                chart: {
                    type: 'donut',
                    height: 300,
                    fontFamily: 'Inter, sans-serif'
                },
                series: @json(collect($bs)->map(fn($v) => $v['count'])->values()),
                labels: @json(collect($bs)->keys()->map(fn($s) => ucfirst($s))->values()),
                colors: ['#d97706', '#2563eb', '#16a34a', '#64748b'],
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
