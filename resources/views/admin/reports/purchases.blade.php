@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                    <li class="is-active">Purchase Report</li>
                </ul>
                <h4 class="a-page-title">Purchase Report</h4>
                <p class="a-page-desc">Procurement by supplier and product · {{ $rangeLabel }}</p>
            </div>
        </div>

        @include('admin.reports.partials.filter', [
            'route' => route('admin.reports.purchases'),
            'reportTitle' => 'Purchase Report',
            'exportReport' => 'purchases',
            'showSearch' => true,
            'searchPlaceholder' => 'Supplier or purchase ID',
            'selects' => [
                ['name' => 'supplier', 'label' => 'Supplier', 'options' => ['' => 'All'] + $suppliers->pluck('name', 'id')->toArray()],
            ],
        ])

        @include('admin.reports.partials.kpi', [
            'cards' => [
                ['label' => 'Total Purchases', 'value' => number_format($summary['count']), 'icon' => 'ri-shopping-cart-2-line', 'color' => 'primary'],
                ['label' => 'Purchase Amount', 'value' => '৳ ' . number_format($summary['amount'], 2), 'icon' => 'ri-money-dollar-circle-line', 'color' => 'info'],
                ['label' => 'Purchase Returns', 'value' => '৳ ' . number_format($summary['return_amount'], 2), 'icon' => 'ri-arrow-go-back-line', 'color' => 'warning', 'sub' => number_format($summary['return_count']) . ' returns', 'delta' => 'flat'],
                ['label' => 'Net Purchase', 'value' => '৳ ' . number_format($summary['net'], 2), 'icon' => 'ri-scales-3-line', 'color' => 'success', 'hint' => 'Purchase Amount − Returns.'],
            ],
        ])

        <div class="a-card mb-3">
            <div class="a-card-head">
                <h5>Purchase Orders</h5>
            </div>
            <div class="table-responsive">
                <table class="table all-package theme-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Purchase ID</th>
                            <th>Supplier</th>
                            <th class="a-table-num">Items</th>
                            <th class="a-table-num">Qty</th>
                            <th class="a-table-num">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($purchases as $p)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($p->date)->format('d M Y') }}</td>
                                <td class="a-cell-main">#{{ $p->id }}</td>
                                <td>{{ $p->supplier }}</td>
                                <td class="a-table-num">{{ $p->items_count }}</td>
                                <td class="a-table-num">{{ number_format($p->qty) }}</td>
                                <td class="a-table-num">৳ {{ number_format($p->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
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
            @if ($purchases->hasPages())
                <div class="a-card-footer">{{ $purchases->links() }}</div>
            @endif
        </div>

        <div class="row g-3">
            <div class="col-xxl-6">
                <div class="a-card h-100">
                    <div class="a-card-head">
                        <h5>Supplier-wise Purchases</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table all-package theme-table">
                            <thead>
                                <tr>
                                    <th>Supplier</th>
                                    <th class="a-table-num">Purchases</th>
                                    <th class="a-table-num">Amount</th>
                                    <th class="a-table-num">Returns</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($supplierWise as $s)
                                    <tr>
                                        <td class="a-cell-main">{{ $s->supplier }}</td>
                                        <td class="a-table-num">{{ number_format($s->purchases) }}</td>
                                        <td class="a-table-num">৳ {{ number_format($s->amount, 2) }}</td>
                                        <td class="a-table-num">৳ {{ number_format($s->return_amount, 2) }}</td>
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
                        <h5>Product-wise Purchases</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table all-package theme-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th class="a-table-num">Qty</th>
                                    <th class="a-table-num">Cost</th>
                                    <th class="a-table-num">Returned</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($productWise as $p)
                                    <tr>
                                        <td class="a-cell-main">{{ $p->product ?? '—' }}</td>
                                        <td>{{ $p->sku ?? '—' }}</td>
                                        <td class="a-table-num">{{ number_format($p->qty) }}</td>
                                        <td class="a-table-num">৳ {{ number_format($p->cost, 2) }}</td>
                                        <td class="a-table-num">{{ number_format($p->returned_qty) }}</td>
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

        <div class="alert alert-secondary mt-3 mb-0">
            <i class="ri-information-line"></i> Paid/Due amounts are not tracked for purchases in this application, so
            they are not shown.
        </div>
    </div>
@endsection

@section('customJs')
@endsection
