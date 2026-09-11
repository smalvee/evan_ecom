@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div class="card-body">
                        <div class="title-header option-title">
                            <h5>Coupon List</h5>
                            <form class="d-inline-flex">
                                <a href="{{ route('coupon.create') }}" class="btn btn-primary">New Coupon</a>
                            </form>
                        </div>

                        <div class="table-responsive category-table">
                            <div>
                                <table class="table all-package theme-table" id="table_id">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>code</th>
                                            <th>Name</th>
                                            <th>description</th>
                                            <th>max_uses</th>
                                            <th>max_uses_user</th>
                                            <th>type</th>
                                            <th>discount_amount</th>
                                            <th>min_amount</th>
                                            <th>starts_at</th>
                                            <th>expires_at</th>
                                            {{-- <th width="100">Status</th> --}}
                                            {{-- <th width="100">Action</th> --}}
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @if ($coupons->isNotEmpty())
                                            @foreach ($coupons as $coupon)
                                                <tr>
                                                    <td>{{ $coupon->id }}</td>
                                                    <td>{{ $coupon->code }}</td>
                                                    <td>{{ $coupon->name }}</td>
                                                    <td>{{ $coupon->description }}</td>
                                                    <td>{{ $coupon->max_uses }}</td>
                                                    <td>{{ $coupon->max_uses_user }}</td>
                                                    <td>{{ $coupon->type }}</td>
                                                    <td>{{ $coupon->discount_amount }}</td>
                                                    <td>{{ $coupon->min_amount }}</td>
                                                    <td>{{ $coupon->starts_at }}</td>
                                                    <td>{{ $coupon->expires_at }}</td>

                                                    {{-- <td>
                                                        @if ($coupon->status == 1)
                                                            <svg class="text-success-500 h-6 w-6 text-success"
                                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                                aria-hidden="true">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                                                </path>
                                                            </svg>
                                                        @else
                                                            <svg class="text-danger h-6 w-6"
                                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                                aria-hidden="true">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z">
                                                                </path>
                                                            </svg>
                                                        @endif

                                                    </td> --}}
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5">Records Not Found</td>
                                            </tr>
                                        @endif

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer clearfix">
                            {{ $coupons->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customJs')
@endsection
