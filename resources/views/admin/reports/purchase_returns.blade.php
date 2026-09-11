@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                    <li class="is-active">Purchase Return Report</li>
                </ul>
                <h4 class="a-page-title">Purchase Return Report</h4>
                <p class="a-page-desc">Returns to suppliers · {{ $rangeLabel }}</p>
            </div>
        </div>

        @include('admin.reports.partials.filter', [
            'route' => route('admin.reports.purchase_returns'),
            'reportTitle' => 'Purchase Return Report',
            'exportReport' => 'purchase_returns',
            'showSearch' => true,
            'searchPlaceholder' => 'Product, SKU or supplier',
        ])

        @include('admin.reports.partials.kpi', [
            'cards' => [
                ['label' => 'Total Returns', 'value' => number_format($summary['count']), 'icon' => 'ri-arrow-go-back-line', 'color' => 'primary'],
                ['label' => 'Returned Quantity', 'value' => number_format($summary['qty']), 'icon' => 'ri-stack-line', 'color' => 'info'],
                ['label' => 'Returned Value', 'value' => '৳ ' . number_format($summary['amount'], 2), 'icon' => 'ri-money-dollar-circle-line', 'color' => 'warning'],
            ],
        ])

        <div class="a-card">
            <div class="a-card-head">
                <h5>Return Items</h5>
            </div>
            <div class="table-responsive">
                <table class="table all-package theme-table">
                    <thead>
                        <tr>
                            <th>Return ID</th>
                            <th>Purchase ID</th>
                            <th>Supplier</th>
                            <th>Product</th>
                            <th class="a-table-num">Qty</th>
                            <th class="a-table-num">Unit Cost</th>
                            <th class="a-table-num">Return Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($returns as $r)
                            <tr>
                                <td class="a-cell-main">#{{ $r->return_id }}</td>
                                <td>#{{ $r->purchase_id }}</td>
                                <td>{{ $r->supplier }}</td>
                                <td>{{ $r->product ?? '—' }}</td>
                                <td class="a-table-num">{{ number_format($r->qty) }}</td>
                                <td class="a-table-num">৳ {{ number_format($r->unit_cost, 2) }}</td>
                                <td class="a-table-num">৳ {{ number_format($r->line_amount, 2) }}</td>
                                <td>{{ $r->date ? \Carbon\Carbon::parse($r->date)->format('d M Y') : '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="a-empty">
                                        <div class="a-empty-icon"><i class="ri-inbox-line"></i></div>
                                        <h5>No data found</h5>
                                        <p>No purchase returns match the selected filters.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($returns->hasPages())
                <div class="a-card-footer">{{ $returns->links() }}</div>
            @endif
        </div>
    </div>
@endsection

@section('customJs')
@endsection
