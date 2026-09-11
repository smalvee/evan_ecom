@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('admin.reports.index') }}">Reports</a></li>
                    <li class="is-active">Coupon Report</li>
                </ul>
                <h4 class="a-page-title">Coupon Report</h4>
                <p class="a-page-desc">Coupon usage and generated revenue · {{ $rangeLabel }}</p>
            </div>
        </div>

        @include('admin.reports.partials.filter', [
            'route' => route('admin.reports.coupons'),
            'reportTitle' => 'Coupon Report',
            'exportReport' => 'coupons',
        ])

        @php
            $totalUsed = $coupons->sum('used');
            $totalDiscount = $coupons->sum('discount_given');
            $totalRevenue = $coupons->sum('revenue');
            $activeCoupons = $coupons->where('status', 1)->count();
        @endphp

        @include('admin.reports.partials.kpi', [
            'cards' => [
                ['label' => 'Coupons', 'value' => number_format($coupons->count()), 'icon' => 'ri-coupon-3-line', 'color' => 'primary', 'sub' => $activeCoupons . ' active', 'delta' => 'flat'],
                ['label' => 'Total Uses', 'value' => number_format($totalUsed), 'icon' => 'ri-shopping-bag-3-line', 'color' => 'info'],
                ['label' => 'Discount Given', 'value' => '৳ ' . number_format($totalDiscount, 2), 'icon' => 'ri-price-tag-3-line', 'color' => 'warning'],
                ['label' => 'Revenue Generated', 'value' => '৳ ' . number_format($totalRevenue, 2), 'icon' => 'ri-money-dollar-circle-line', 'color' => 'success'],
            ],
        ])

        <div class="a-card">
            <div class="a-card-head">
                <h5>Coupons</h5>
            </div>
            <div class="table-responsive">
                <table class="table all-package theme-table">
                    <thead>
                        <tr>
                            <th>Coupon Code</th>
                            <th>Type</th>
                            <th class="a-table-num">Value</th>
                            <th class="a-table-num">Used</th>
                            <th class="a-table-num">Discount Given</th>
                            <th class="a-table-num">Revenue Generated</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($coupons as $c)
                            <tr>
                                <td class="a-cell-main">{{ $c->code }}</td>
                                <td>{{ ucfirst($c->type) }}</td>
                                <td class="a-table-num">
                                    {{ $c->type === 'percent' ? rtrim(rtrim(number_format($c->discount_amount, 2), '0'), '.') . '%' : '৳ ' . number_format($c->discount_amount, 2) }}
                                </td>
                                <td class="a-table-num">{{ number_format($c->used) }}</td>
                                <td class="a-table-num">৳ {{ number_format($c->discount_given, 2) }}</td>
                                <td class="a-table-num">৳ {{ number_format($c->revenue, 2) }}</td>
                                <td>
                                    @if ($c->status == 1)
                                        <span class="a-badge a-badge-success"><span class="dot"></span>Active</span>
                                    @else
                                        <span class="a-badge a-badge-secondary"><span class="dot"></span>Inactive</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="a-empty">
                                        <div class="a-empty-icon"><i class="ri-inbox-line"></i></div>
                                        <h5>No coupons found</h5>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('customJs')
@endsection
