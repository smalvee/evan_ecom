@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                    <li class="is-active">Inventory Report</li>
                </ul>
                <h4 class="a-page-title">Inventory Report</h4>
                <p class="a-page-desc">Current stock levels and stock value (at cost)</p>
            </div>
        </div>

        @include('admin.reports.partials.filter', [
            'route' => route('admin.reports.inventory'),
            'reportTitle' => 'Inventory Report',
            'exportReport' => 'inventory',
            'showRange' => false,
            'showSearch' => true,
            'searchPlaceholder' => 'Product name or SKU',
            'selects' => [
                ['name' => 'stock', 'label' => 'Stock Status', 'options' => ['' => 'All', 'in' => 'In Stock', 'low' => 'Low Stock', 'out' => 'Out of Stock']],
            ],
        ])

        @include('admin.reports.partials.kpi', [
            'cards' => [
                ['label' => 'Total Products', 'value' => number_format($summary['products']), 'icon' => 'ri-store-3-line', 'color' => 'primary', 'sub' => $summary['variants'] . ' variants', 'delta' => 'flat'],
                ['label' => 'Total Stock Units', 'value' => number_format($summary['units']), 'icon' => 'ri-stack-line', 'color' => 'info'],
                ['label' => 'Low Stock', 'value' => number_format($summary['low_stock']), 'icon' => 'ri-alert-line', 'color' => 'warning', 'sub' => '≤ ' . $summary['threshold'] . ' units', 'delta' => 'flat'],
                ['label' => 'Out of Stock', 'value' => number_format($summary['out_of_stock']), 'icon' => 'ri-close-circle-line', 'color' => 'danger'],
                ['label' => 'Stock Value (at cost)', 'value' => '৳ ' . number_format($summary['stock_value'], 0), 'icon' => 'ri-money-dollar-circle-line', 'color' => 'success', 'hint' => 'Positive stock × current weighted-average cost.'],
            ],
        ])

        <div class="a-card">
            <div class="a-card-head">
                <h5>Inventory</h5>
            </div>
            <div class="table-responsive">
                <table class="table all-package theme-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th class="a-table-num">Stock</th>
                            <th class="a-table-num">Purchase Cost</th>
                            <th class="a-table-num">Stock Value</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($variants as $index => $v)
                            @php
                                $qty = (int) $v->qty;
                                $cost = (float) ($v->average_cost ?? $v->purchase_price);
                                $value = $qty > 0 ? $qty * $cost : 0;
                            @endphp
                            <tr>
                                <td>{{ $variants->firstItem() + $index }}</td>
                                <td class="a-cell-main">{{ $v->product ?? '—' }}</td>
                                <td>{{ $v->sku ?? '—' }}</td>
                                <td>{{ $v->category ?? '—' }}</td>
                                <td class="a-table-num">{{ number_format($qty) }}</td>
                                <td class="a-table-num">৳ {{ number_format($cost, 2) }}</td>
                                <td class="a-table-num">৳ {{ number_format($value, 2) }}</td>
                                <td>
                                    @if ($qty <= 0)
                                        <span class="a-badge a-badge-danger"><span class="dot"></span>Out of Stock</span>
                                    @elseif ($qty <= $summary['threshold'])
                                        <span class="a-badge a-badge-warning"><span class="dot"></span>Low Stock</span>
                                    @else
                                        <span class="a-badge a-badge-success"><span class="dot"></span>In Stock</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="a-empty">
                                        <div class="a-empty-icon"><i class="ri-inbox-line"></i></div>
                                        <h5>No data found</h5>
                                        <p>No inventory matches the selected filters.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($variants->hasPages())
                <div class="a-card-footer">{{ $variants->links() }}</div>
            @endif
        </div>
    </div>
@endsection

@section('customJs')
@endsection
