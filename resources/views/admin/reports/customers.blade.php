@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                    <li class="is-active">Customer Report</li>
                </ul>
                <h4 class="a-page-title">Customer Report</h4>
                <p class="a-page-desc">Customer activity and lifetime value · {{ $rangeLabel }}</p>
            </div>
        </div>

        @include('admin.reports.partials.filter', [
            'route' => route('admin.reports.customers'),
            'reportTitle' => 'Customer Report',
            'exportReport' => 'customers',
            'showSearch' => true,
            'searchPlaceholder' => 'Name, email or phone',
            'selects' => [
                ['name' => 'top', 'label' => 'Show Top', 'options' => ['10' => 'Top 10', '20' => 'Top 20', '50' => 'Top 50', '200' => 'All']],
            ],
        ])

        @include('admin.reports.partials.kpi', [
            'cards' => [
                ['label' => 'Total Customers', 'value' => number_format($summary['total']), 'icon' => 'ri-user-3-line', 'color' => 'primary'],
                ['label' => 'New Customers', 'value' => number_format($summary['new']), 'icon' => 'ri-user-add-line', 'color' => 'info', 'sub' => 'in selected period', 'delta' => 'flat'],
                ['label' => 'Active Customers', 'value' => number_format($summary['active']), 'icon' => 'ri-user-follow-line', 'color' => 'success', 'sub' => 'ordered in period', 'delta' => 'flat'],
                ['label' => 'Returning Customers', 'value' => number_format($summary['returning']), 'icon' => 'ri-refresh-line', 'color' => 'warning', 'sub' => '2+ orders', 'delta' => 'flat'],
            ],
        ])

        <div class="a-card">
            <div class="a-card-head">
                <h5>Customers (sorted by lifetime value)</h5>
                <span class="text-muted small">{{ number_format($summary['with_orders']) }} customers have placed orders</span>
            </div>
            <div class="table-responsive">
                <table class="table all-package theme-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th class="a-table-num">Orders</th>
                            <th class="a-table-num">Total Spent</th>
                            <th class="a-table-num">Avg Order</th>
                            <th>Last Order</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customers as $index => $c)
                            <tr>
                                <td>{{ $customers->firstItem() + $index }}</td>
                                <td class="a-cell-main">{{ $c->name }}</td>
                                <td>
                                    <div>{{ $c->email }}</div>
                                    <small class="a-cell-sub">{{ $c->phone }}</small>
                                </td>
                                <td class="a-table-num">{{ number_format($c->orders_count) }}</td>
                                <td class="a-table-num">৳ {{ number_format($c->total_spent, 2) }}</td>
                                <td class="a-table-num">৳
                                    {{ $c->orders_count > 0 ? number_format($c->total_spent / $c->orders_count, 2) : '0.00' }}
                                </td>
                                <td>{{ $c->last_order ? \Carbon\Carbon::parse($c->last_order)->format('d M Y') : '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="a-empty">
                                        <div class="a-empty-icon"><i class="ri-inbox-line"></i></div>
                                        <h5>No data found</h5>
                                        <p>There is no customer data matching the selected filters.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($customers->hasPages())
                <div class="a-card-footer">{{ $customers->links() }}</div>
            @endif
        </div>

        <div class="alert alert-secondary mt-3 mb-0">
            <i class="ri-information-line"></i> <strong>Customer Lifetime Value</strong> = total qualifying revenue from
            non-cancelled orders per customer (shown as "Total Spent").
        </div>
    </div>
@endsection

@section('customJs')
@endsection
