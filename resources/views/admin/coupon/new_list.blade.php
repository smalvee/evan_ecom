@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="is-active">Coupons</li>
                </ul>
                <h4 class="a-page-title">Coupons</h4>
                <p class="a-page-desc">Manage discount coupons for your store.</p>
            </div>
            <div class="a-actions">
                <a href="{{ route('coupon.create') }}" class="btn btn-theme"><i class="ri-add-line"></i> New Coupon</a>
            </div>
        </div>

        <div class="a-card">
            <div class="a-card-head">
                <h5>Coupon List</h5>
            </div>
            <div class="table-responsive">
                <table class="table all-package theme-table" id="table_id">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Max Uses</th>
                            <th>Max Uses User</th>
                            <th>Type</th>
                            <th>Discount Amount</th>
                            <th>Min Amount</th>
                            <th>Starts At</th>
                            <th>Expires At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($coupons->isNotEmpty())
                            @foreach ($coupons as $coupon)
                                <tr>
                                    <td>{{ $coupon->id }}</td>
                                    <td><span class="a-cell-main">{{ $coupon->code }}</span></td>
                                    <td>{{ $coupon->name }}</td>
                                    <td>{{ $coupon->description }}</td>
                                    <td>{{ $coupon->max_uses }}</td>
                                    <td>{{ $coupon->max_uses_user }}</td>
                                    <td>{{ $coupon->type }}</td>
                                    <td>{{ $coupon->discount_amount }}</td>
                                    <td>{{ $coupon->min_amount }}</td>
                                    <td>{{ $coupon->starts_at }}</td>
                                    <td>{{ $coupon->expires_at }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="11">
                                    <div class="a-empty">
                                        <div class="a-empty-icon"><i class="ri-inbox-line"></i></div>
                                        <h5>No coupons found</h5>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="a-card-footer">
                {{ $coupons->links() }}
            </div>
        </div>
    </div>
@endsection

@section('customJs')
@endsection
