@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                    <li class="is-active">Product Sales</li>
                </ul>
                <h4 class="a-page-title">Product Sales Report</h4>
                <p class="a-page-desc">Revenue, cost and profit by product · {{ $rangeLabel }}</p>
            </div>
        </div>

        @include('admin.reports.partials.filter', [
            'route' => route('item_sales.index'),
            'reportTitle' => 'Product Sales Report',
            'exportReport' => 'product_sales',
            'showSearch' => true,
            'searchPlaceholder' => 'Product name or SKU',
            'selects' => [
                ['name' => 'status', 'label' => 'Order Status', 'options' => ['' => 'All'] + $statuses],
                ['name' => 'category', 'label' => 'Category', 'options' => ['' => 'All'] + $categories->pluck('name', 'id')->toArray()],
                ['name' => 'brand', 'label' => 'Brand', 'options' => ['' => 'All'] + $brands->pluck('name', 'id')->toArray()],
            ],
        ])

        @php
            $totalUnits = $products->sum('units');
            $totalRevenue = $products->sum('revenue');
            $totalCost = $products->sum('cost');
            $totalProfit = $totalRevenue - $totalCost;
        @endphp

        @include('admin.reports.partials.kpi', [
            'cards' => [
                ['label' => 'Units Sold (page)', 'value' => number_format($totalUnits), 'icon' => 'ri-shopping-bag-3-line', 'color' => 'info'],
                ['label' => 'Revenue (page)', 'value' => '৳ ' . number_format($totalRevenue, 2), 'icon' => 'ri-money-dollar-circle-line', 'color' => 'primary'],
                ['label' => 'Cost (page)', 'value' => '৳ ' . number_format($totalCost, 2), 'icon' => 'ri-shopping-cart-2-line', 'color' => 'warning', 'hint' => 'Variant purchase price × qty.'],
                ['label' => 'Profit (page)', 'value' => '৳ ' . number_format($totalProfit, 2), 'icon' => 'ri-line-chart-line', 'color' => 'success', 'sub' => $totalRevenue > 0 ? round(($totalProfit / $totalRevenue) * 100, 2) . '% margin' : null, 'delta' => 'flat'],
            ],
        ])

        <div class="a-card">
            <div class="a-card-head">
                <h5>Product Sales</h5>
            </div>
            <div class="table-responsive">
                <table class="table all-package theme-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th class="a-table-num">Units</th>
                            <th class="a-table-num">Revenue</th>
                            <th class="a-table-num">Cost</th>
                            <th class="a-table-num">Profit</th>
                            <th class="a-table-num">Margin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $index => $p)
                            @php
                                $profit = (float) $p->revenue - (float) $p->cost;
                                $margin = (float) $p->revenue > 0 ? ($profit / (float) $p->revenue) * 100 : 0;
                            @endphp
                            <tr>
                                <td>{{ $products->firstItem() + $index }}</td>
                                <td class="a-cell-main">{{ $p->product ?? '—' }}</td>
                                <td>{{ $p->sku ?? '—' }}</td>
                                <td>{{ $p->category ?? '—' }}</td>
                                <td>{{ $p->brand ?? '—' }}</td>
                                <td class="a-table-num">{{ number_format($p->units) }}</td>
                                <td class="a-table-num">৳ {{ number_format($p->revenue, 2) }}</td>
                                <td class="a-table-num">৳ {{ number_format($p->cost, 2) }}</td>
                                <td class="a-table-num {{ $profit >= 0 ? 'text-success' : 'text-danger' }}">৳
                                    {{ number_format($profit, 2) }}</td>
                                <td class="a-table-num">{{ round($margin, 1) }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10">
                                    <div class="a-empty">
                                        <div class="a-empty-icon"><i class="ri-inbox-line"></i></div>
                                        <h5>No data found</h5>
                                        <p>There is no product sales data matching the selected filters.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($products->hasPages())
                <div class="a-card-footer">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('customJs')
@endsection
