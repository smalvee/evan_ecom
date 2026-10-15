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
            font-size: 11px;
            letter-spacing: .01em;
            padding: 6px 6px !important;
        }

        /* Eye-catching group headers (solid + white text) */
        .stock-report-table .a-group-head.sec-opening { background: #6366f1 !important; color: #ffffff !important; }
        .stock-report-table .a-group-head.sec-purchase { background: #10b981 !important; color: #ffffff !important; }
        .stock-report-table .a-group-head.sec-sales { background: #f43f5e !important; color: #ffffff !important; }
        .stock-report-table .a-group-head.sec-closing { background: #8b5cf6 !important; color: #ffffff !important; }

        /* Section body tints (light background + colored text) */
        .stock-report-table .sec-opening { background: #eef2ff !important; color: #4338ca; }
        .stock-report-table .sec-purchase { background: #ecfdf5 !important; color: #047857; }
        .stock-report-table .sec-sales { background: #fff1f2 !important; color: #be123c; }
        .stock-report-table .sec-closing { background: #f5f3ff !important; color: #6d28d9; }

        /* Vertical divider between the four stock sections */
        .stock-report-table .sec-start {
            border-left: 2px solid var(--a-border-strong);
        }
        .stock-report-table .sec-end {
            border-right: 2px solid var(--a-border-strong);
        }

        /* Money / numeric columns right-aligned + compact */
        .stock-report-table th.a-table-num,
        .stock-report-table td.a-table-num {
            text-align: right !important;
            padding: 2px 5px !important;
            font-size: 11px;
            white-space: nowrap;
        }

        /* Frozen first two columns */
        .stock-report-table .col-sku,
        .stock-report-table .col-name {
            position: sticky;
            z-index: 3;
        }
        .stock-report-table .col-sku {
            left: 0;
            width: 130px;
            min-width: 130px;
            max-width: 130px;
        }
        .stock-report-table .col-name {
            left: 130px;
            width: 170px;
            min-width: 170px;
            max-width: 170px;
            box-shadow: 4px 0 6px -4px rgba(16, 24, 40, 0.12);
        }
        .stock-report-table td.col-sku,
        .stock-report-table td.col-name {
            background-color: #ffffff;
        }
        .stock-report-table thead th.col-sku,
        .stock-report-table thead th.col-name {
            background-color: #f3f3f3 !important;
        }
        .stock-report-table td.col-sku,
        .stock-report-table td.col-name {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
                $v = round((float) $v, 2);
                return '৳' . ($v == (int) $v ? number_format($v, 0) : number_format($v, 2));
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
                            <th rowspan="2" class="align-middle col-sku">SKU No</th>
                            <th rowspan="2" class="align-middle col-name">SKU Name</th>
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
                                <td class="text-nowrap col-sku" title="{{ $v->sku }}">{{ $v->sku }}</td>
                                <td class="a-cell-main col-name" title="{{ $v->product }}">{{ $v->product }}</td>

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
