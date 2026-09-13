@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="is-active">Orders</li>
                </ul>
                <h4 class="a-page-title">Orders</h4>
                <p class="a-page-desc">Manage and track customer orders.</p>
            </div>
            <div class="a-actions">
                <a href="{{ route('orders.create') }}" class="btn btn-theme"><i class="ri-add-line"></i> Create Order</a>
            </div>
        </div>

        <div class="a-card">
            <div class="a-card-head">
                <h5>Order List</h5>
                <form action="{{ route('orders.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
                    <select name="status" class="form-select" style="width:auto;">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirm" {{ request('status') == 'confirm' ? 'selected' : '' }}>Confirmed</option>
                        <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancell" {{ request('status') == 'cancell' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <div style="position: relative;">
                        <input type="text" class="form-control" placeholder="Search order, name, phone"
                            value="{{ request('keyword') }}" name="keyword" style="padding-right: 34px;">
                        <button type="submit"
                            style="position: absolute; right: 6px; top: 50%; transform: translateY(-50%); border: none; background: none; cursor: pointer;">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <button type="submit" class="btn btn-theme btn-sm"><i class="ri-filter-2-line"></i> Filter</button>
                    @if (request('status') || request('keyword'))
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary btn-sm"><i
                                class="ri-refresh-line"></i> Reset</a>
                    @endif
                </form>
            </div>
            <div class="table-responsive">
                <table class="table all-package theme-table" id="table_id">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th class="text-end">Total</th>
                            <th>Admin Note</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, h:i A') }}</td>
                                <td>
                                    <a href="{{ route('orders.details', $order->id) }}" class="a-cell-main"
                                        style="text-decoration: none;">
                                        {{ $order->order_id }}
                                    </a>
                                    @if ($order->items->contains(fn($i) => (int) $i->free_delivery === 1))
                                        <div>
                                            <span
                                                style="font-size:10px;padding:2px 6px;background:#198754;color:#fff;border-radius:4px;">Free
                                                Delivery</span>
                                        </div>
                                    @endif
                                    @if ($order->items->contains(fn($i) => $i->is_pre_order))
                                        <div>
                                            <span
                                                style="font-size:10px;padding:2px 6px;background:#f59e0b;color:#fff;border-radius:4px;">Pre
                                                Order</span>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="a-cell-main"
                                        style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                        {{ $order->name }}
                                    </div>
                                </td>
                                <td>{{ $order->phone }}</td>
                                <td class="text-end">
                                    <span class="a-cell-main">৳ {{ number_format($order->grand_total) }}</span>
                                </td>
                                <td>{{ $order->admin_note }}</td>
                                <td>
                                    @if ($order->status == 'pending')
                                        <span class="a-badge a-badge-danger"><span class="dot"></span>Pending</span>
                                    @elseif ($order->status == 'confirm')
                                        <span class="a-badge a-badge-info"><span class="dot"></span>Confirmed</span>
                                    @elseif ($order->status == 'shipped')
                                        <span class="a-badge a-badge-success"><span class="dot"></span>Delivered</span>
                                    @elseif ($order->status == 'cancell')
                                        <span class="a-badge a-badge-secondary"><span class="dot"></span>Cancelled</span>
                                    @else
                                        <span class="a-badge a-badge-neutral"><span
                                                class="dot"></span>{{ $order->status }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="a-actions-cell justify-content-end">
                                        <a href="{{ route('orders.details', $order->id) }}" class="a-action-btn a-view"
                                            title="View"><i class="ri-eye-line"></i></a>
                                        @if (in_array($order->status, ['confirm', 'shipped'], true))
                                            <a href="javascript:void(0)" class="a-action-btn"
                                                style="opacity:.45;cursor:not-allowed;"
                                                title="Confirmed orders cannot be deleted — cancel instead"
                                                onclick="cannotDeleteOrder()"><i class="ri-lock-line"></i></a>
                                        @else
                                            <a href="javascript:void(0)" onclick="deleteOrder({{ $order->id }})"
                                                class="a-action-btn a-danger" title="Delete"><i
                                                    class="ri-delete-bin-line"></i></a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="a-empty">
                                        <div class="a-empty-icon"><i class="ri-inbox-line"></i></div>
                                        <h5>No orders found</h5>
                                        <p>There are no orders matching your current filters.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($orders->hasPages())
                <div class="a-card-footer">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('customJs')
    <script>
        function cannotDeleteOrder() {
            Swal.fire({
                icon: 'info',
                title: 'Cannot delete',
                text: 'Confirmed or shipped orders cannot be deleted. Please cancel the order instead.'
            });
        }

        function deleteOrder(id) {
            Swal.fire({
                title: 'Delete this order?',
                text: 'This action cannot be undone. The order and its items will be permanently removed.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (!result.isConfirmed) return;

                var url = '{{ route('orders.delete', 'ID') }}'.replace('ID', id);

                $.ajax({
                    url: url,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            setTimeout(function() {
                                window.location.reload();
                            }, 1200);
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
                            title: 'Something went wrong',
                            text: 'Unable to delete the order.'
                        });
                    }
                });
            });
        }
    </script>
@endsection
