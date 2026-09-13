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
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($coupons->isNotEmpty())
                            @foreach ($coupons as $coupon)
                                <tr>
                                    <td>{{ $coupon->id }}</td>
                                    <td><span class="a-cell-main">{{ $coupon->code }}</span></td>
                                    <td>{{ $coupon->name }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($coupon->description, 40) }}</td>
                                    <td>{{ $coupon->max_uses }}</td>
                                    <td>{{ $coupon->max_uses_user }}</td>
                                    <td>{{ $coupon->type }}</td>
                                    <td>{{ $coupon->discount_amount }}</td>
                                    <td>{{ $coupon->min_amount }}</td>
                                    <td>{{ $coupon->starts_at ? \Carbon\Carbon::parse($coupon->starts_at)->format('d M Y') : '—' }}
                                    </td>
                                    <td>{{ $coupon->expires_at ? \Carbon\Carbon::parse($coupon->expires_at)->format('d M Y') : '—' }}
                                    </td>
                                    <td>
                                        @if ($coupon->status == 1)
                                            <span class="a-badge a-badge-success"><span class="dot"></span>Active</span>
                                        @else
                                            <span class="a-badge a-badge-secondary"><span class="dot"></span>Block</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="a-actions-cell justify-content-end">
                                            <a href="{{ route('coupon.edit', $coupon->id) }}" class="a-action-btn a-view"
                                                title="Edit"><i class="ri-pencil-line"></i></a>
                                            <a href="javascript:void(0)" onclick="deleteCoupon({{ $coupon->id }})"
                                                class="a-action-btn a-danger" title="Delete"><i
                                                    class="ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="13">
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
    <script>
        function deleteCoupon(id) {
            Swal.fire({
                title: 'Delete this coupon?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (!result.isConfirmed) return;

                var url = '{{ route('coupon.delete', 'ID') }}'.replace('ID', id);

                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                timer: 1200,
                                showConfirmButton: false
                            });
                            setTimeout(function() {
                                window.location.reload();
                            }, 1000);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Something went wrong'
                        });
                    }
                });
            });
        }
    </script>
@endsection
