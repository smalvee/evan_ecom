@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                    <li class="is-active">Order Report</li>
                </ul>
                <h4 class="a-page-title">Order Report</h4>
                <p class="a-page-desc">Order status and payment breakdown · {{ $rangeLabel }}</p>
            </div>
        </div>

        @include('admin.reports.partials.filter', [
            'route' => route('admin.reports.orders'),
            'reportTitle' => 'Order Report',
            'exportReport' => 'orders',
            'showSearch' => true,
            'searchPlaceholder' => 'Order ID, customer or phone',
            'selects' => [
                ['name' => 'status', 'label' => 'Order Status', 'options' => ['' => 'All'] + $statuses],
                ['name' => 'payment', 'label' => 'Payment', 'options' => ['' => 'All'] + $payments],
            ],
        ])

        @php
            $st = fn($s) => $summary['by_status'][$s]['count'] ?? 0;
        @endphp

        @include('admin.reports.partials.kpi', [
            'cards' => [
                ['label' => 'Total Orders', 'value' => number_format($summary['total']), 'icon' => 'ri-shopping-bag-3-line', 'color' => 'primary'],
                ['label' => 'Pending', 'value' => number_format($st('pending')), 'icon' => 'ri-time-line', 'color' => 'danger'],
                ['label' => 'Confirmed', 'value' => number_format($st('confirm')), 'icon' => 'ri-check-double-line', 'color' => 'info'],
                ['label' => 'Delivered', 'value' => number_format($st('shipped')), 'icon' => 'ri-truck-line', 'color' => 'success'],
                ['label' => 'Cancelled', 'value' => number_format($st('cancell')), 'icon' => 'ri-close-circle-line', 'color' => 'secondary'],
                ['label' => 'Paid', 'value' => number_format($summary['paid']['count']), 'icon' => 'ri-money-dollar-circle-line', 'color' => 'success', 'sub' => '৳ ' . number_format($summary['paid']['amount'], 0), 'delta' => 'flat'],
                ['label' => 'Unpaid', 'value' => number_format($summary['unpaid']['count']), 'icon' => 'ri-error-warning-line', 'color' => 'warning', 'sub' => '৳ ' . number_format($summary['unpaid']['amount'], 0), 'delta' => 'flat'],
                ['label' => 'Avg Order Value', 'value' => $summary['total'] > 0 ? '৳ ' . number_format(($summary['paid']['amount'] + $summary['unpaid']['amount']) / $summary['total'], 0) : '৳ 0', 'icon' => 'ri-calculator-line', 'color' => 'info'],
            ],
        ])

        <div class="a-card">
            <div class="a-card-head">
                <h5>Orders</h5>
            </div>
            <div class="table-responsive">
                <table class="table all-package theme-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th class="a-table-num">Items</th>
                            <th class="a-table-num">Total</th>
                            <th>Payment</th>
                            <th>Status</th>
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
                                <td>
                                    <div class="a-cell-main">{{ $order->name }}</div>
                                    <small class="a-cell-sub">{{ $order->phone }}</small>
                                </td>
                                <td class="a-table-num">{{ $order->items_count }}</td>
                                <td class="a-table-num">৳ {{ number_format($order->grand_total, 2) }}</td>
                                <td>
                                    @if ($order->payment_status)
                                        <span class="a-badge a-badge-success"><span class="dot"></span>Paid</span>
                                    @else
                                        <span class="a-badge a-badge-warning"><span class="dot"></span>Unpaid</span>
                                    @endif
                                </td>
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
                                        <span class="a-badge a-badge-neutral"><span
                                                class="dot"></span>{{ $order->status }}</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="a-empty">
                                        <div class="a-empty-icon"><i class="ri-inbox-line"></i></div>
                                        <h5>No data found</h5>
                                        <p>There is no data matching the selected filters.</p>
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
@endsection
