@extends('admin.layouts.new_app')

@section('content')
    <style>
        .stock-report-table thead th {
            white-space: nowrap;
        }
        .stock-report-table th,
        .stock-report-table td {
            vertical-align: middle;
        }
        .stock-report-table .a-group-head {
            text-align: center;
            font-weight: 700;
        }

        /* Section colors */
        .sec-opening { background: #eef2ff; color: #4338ca; }
        .sec-purchase { background: #ecfdf5; color: #047857; }
        .sec-sales { background: #fff7ed; color: #c2410c; }
        .sec-closing { background: #faf5ff; color: #7e22ce; }

        /* Vertical divider between the four stock sections */
        .stock-report-table .sec-start {
            border-left: 2px solid var(--a-border-strong);
        }
        .stock-report-table .sec-end {
            border-right: 2px solid var(--a-border-strong);
        }
    </style>
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                    <li class="is-active">Stock Report</li>
                </ul>
                <h4 class="a-page-title">Stock Report</h4>
                <p class="a-page-desc">Opening, purchase, sales and closing stock by SKU · {{ $rangeLabel }}</p>
            </div>
        </div>

        @include('admin.reports.partials.filter', [
            'route' => route('admin.reports.stock'),
            'reportTitle' => 'Stock Report',
            'exportReport' => 'stock',
            'showSearch' => true,
            'searchPlaceholder' => 'SKU or product name',
            'selects' => [
                ['name' => 'category', 'label' => 'Category', 'options' => ['' => 'All'] + $categories->pluck('name', 'id')->toArray()],
                ['name' => 'brand', 'label' => 'Brand', 'options' => ['' => 'All'] + $brands->pluck('name', 'id')->toArray()],
            ],
        ])

        @php
            $fmtQty = function ($q) {
                $q = (float) $q;
                return $q == (int) $q ? number_format($q, 0) : number_format($q, 2);
            };
            $fmtMoney = function ($v) {
                return '৳ ' . number_format((float) $v, 2);
            };
        @endphp

        <div class="a-card">
            <div class="a-card-head">
                <h5>Stock Movement</h5>
                <span class="a-badge a-badge-neutral">{{ $rows->total() }} SKUs</span>
            </div>
            <div class="table-responsive">
                <table class="table all-package theme-table stock-report-table">
                    <thead>
                        <tr>
                            <th rowspan="2" class="align-middle">SKU No</th>
                            <th rowspan="2" class="align-middle">SKU Name</th>
                            <th colspan="3" class="text-center a-group-head sec-opening sec-start">Opening Stock</th>
                            <th colspan="3" class="text-center a-group-head sec-purchase sec-start">Purchase</th>
                            <th colspan="3" class="text-center a-group-head sec-sales sec-start">Sales</th>
                            <th colspan="3" class="text-center a-group-head sec-closing sec-start sec-end">Closing Stock</th>
                        </tr>
                        <tr>
                            <th class="a-table-num sec-opening sec-start">Qty</th>
                            <th class="a-table-num sec-opening">Rate</th>
                            <th class="a-table-num sec-opening">Value</th>
                            <th class="a-table-num sec-purchase sec-start">Qty</th>
                            <th class="a-table-num sec-purchase">Rate</th>
                            <th class="a-table-num sec-purchase">Value</th>
                            <th class="a-table-num sec-sales sec-start">Qty</th>
                            <th class="a-table-num sec-sales">Rate</th>
                            <th class="a-table-num sec-sales">Value</th>
                            <th class="a-table-num sec-closing sec-start">Qty</th>
                            <th class="a-table-num sec-closing">Rate</th>
                            <th class="a-table-num sec-closing sec-end">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $v)
                            @php $s = $v->stock; @endphp
                            <tr>
                                <td class="text-nowrap">{{ $v->sku }}</td>
                                <td class="a-cell-main">{{ $v->product }}</td>

                                <td class="a-table-num sec-opening sec-start">{{ $fmtQty($s['opening_qty']) }}</td>
                                <td class="a-table-num sec-opening">{{ $fmtMoney($s['opening_rate']) }}</td>
                                <td class="a-table-num sec-opening">{{ $fmtMoney($s['opening_value']) }}</td>

                                <td class="a-table-num sec-purchase sec-start">{{ $fmtQty($s['purchase_qty']) }}</td>
                                <td class="a-table-num sec-purchase">{{ $fmtMoney($s['purchase_rate']) }}</td>
                                <td class="a-table-num sec-purchase">{{ $fmtMoney($s['purchase_value']) }}</td>

                                <td class="a-table-num sec-sales sec-start">{{ $fmtQty($s['sales_qty']) }}</td>
                                <td class="a-table-num sec-sales">{{ $fmtMoney($s['sales_rate']) }}</td>
                                <td class="a-table-num sec-sales">{{ $fmtMoney($s['sales_value']) }}</td>

                                <td class="a-table-num sec-closing sec-start">{{ $fmtQty($s['closing_qty']) }}</td>
                                <td class="a-table-num sec-closing">{{ $fmtMoney($s['closing_rate']) }}</td>
                                <td class="a-table-num sec-closing sec-end">{{ $fmtMoney($s['closing_value']) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="14">
                                    <div class="a-empty">
                                        <div class="a-empty-icon"><i class="ri-inbox-line"></i></div>
                                        <h5>No data found</h5>
                                        <p>There is no stock data matching the selected filters.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($rows->hasPages())
                <div class="a-card-footer">{{ $rows->links() }}</div>
            @endif
        </div>
    </div>
@endsection

@section('customJs')
@endsection
