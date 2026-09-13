@extends('admin.layouts.new_app')

@section('content')
    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="is-active">Pre Orders</li>
                </ul>
                <h4 class="a-page-title">Pre Orders</h4>
                <p class="a-page-desc">Manage customer pre-orders. Process them explicitly once stock arrives.</p>
            </div>
        </div>

        <div class="a-card">
            <div class="a-card-head">
                <h5>Pre-Order Items</h5>
                <form action="{{ route('admin.pre_orders.index') }}" method="GET"
                    class="d-flex flex-wrap align-items-center gap-2">
                    <select name="status" class="form-select" style="width:auto;">
                        <option value="">All</option>
                        <option value="pending" {{ $filter == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="available" {{ $filter == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="processing" {{ $filter == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ $filter == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $filter == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <div style="position: relative;">
                        <input type="text" class="form-control" placeholder="Search order, customer, product"
                            value="{{ request('keyword') }}" name="keyword" style="padding-right: 34px;">
                        <button type="submit"
                            style="position: absolute; right: 6px; top: 50%; transform: translateY(-50%); border: none; background: none; cursor: pointer;">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <button type="submit" class="btn btn-theme btn-sm"><i class="ri-filter-2-line"></i> Filter</button>
                    @if (request('status') || request('keyword'))
                        <a href="{{ route('admin.pre_orders.index') }}" class="btn btn-outline-secondary btn-sm"><i
                                class="ri-refresh-line"></i> Reset</a>
                    @endif
                </form>
            </div>
            <div class="table-responsive">
                <table class="table all-package theme-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Variant</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Total</th>
                            <th>Order Date</th>
                            <th class="text-end">Current Stock</th>
                            <th>Order Status</th>
                            <th>Payment</th>
                            <th>Pre-order Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($preOrders as $row)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.pre_orders.details', $row->item_id) }}"
                                        class="a-cell-main" style="text-decoration:none;">
                                        {{ $row->order_code }}
                                    </a>
                                </td>
                                <td>
                                    <div class="a-cell-main">{{ $row->customer_name }}</div>
                                    <div class="text-muted" style="font-size:12px;">{{ $row->customer_phone }}</div>
                                </td>
                                <td>{{ $row->product_name ?? '—' }}</td>
                                <td>{{ $row->variant_sku ?? $row->variant_name }}</td>
                                <td class="text-end">{{ (int) $row->qty }}</td>
                                <td class="text-end">৳ {{ number_format((float) $row->price, 2) }}</td>
                                <td class="text-end">৳ {{ number_format((float) $row->total, 2) }}</td>
                                <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d M Y, h:i A') }}</td>
                                <td class="text-end">
                                    {{ $row->stock_value === null ? '∞' : (int) $row->stock_value }}
                                </td>
                                <td>
                                    @php $os = $row->order_status; @endphp
                                    @if ($os == 'pending')
                                        <span class="a-badge a-badge-danger"><span class="dot"></span>Pending</span>
                                    @elseif ($os == 'confirm')
                                        <span class="a-badge a-badge-info"><span class="dot"></span>Confirmed</span>
                                    @elseif ($os == 'shipped')
                                        <span class="a-badge a-badge-success"><span class="dot"></span>Delivered</span>
                                    @elseif ($os == 'cancell')
                                        <span class="a-badge a-badge-secondary"><span class="dot"></span>Cancelled</span>
                                    @else
                                        <span class="a-badge a-badge-neutral"><span
                                                class="dot"></span>{{ $os }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($row->payment_status)
                                        <span class="a-badge a-badge-success"><span class="dot"></span>Paid</span>
                                    @else
                                        <span class="a-badge a-badge-warning"><span class="dot"></span>Unpaid</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($row->pre_order_state == 'pending')
                                        <span class="a-badge a-badge-danger"><span class="dot"></span>Pending</span>
                                    @elseif ($row->pre_order_state == 'available')
                                        <span class="a-badge a-badge-success"><span class="dot"></span>Stock Available</span>                                    @elseif ($row->pre_order_state == 'processing')
                                        <span class="a-badge a-badge-info"><span class="dot"></span>Processing</span>
                                    @elseif ($row->pre_order_state == 'completed')
                                        <span class="a-badge a-badge-success"><span class="dot"></span>Completed</span>
                                    @elseif ($row->pre_order_state == 'cancelled')
                                        <span class="a-badge a-badge-secondary"><span
                                                class="dot"></span>Cancelled</span>
                                    @else
                                        <span class="a-badge a-badge-neutral"><span
                                                class="dot"></span>{{ ucfirst($row->pre_order_state) }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="a-actions-cell justify-content-end">
                                        <a href="{{ route('admin.pre_orders.details', $row->item_id) }}"
                                            class="a-action-btn a-view" title="View"><i class="ri-eye-line"></i></a>
                                        @if (in_array($row->pre_order_state, ['processing', 'completed', 'cancelled'], true))
                                            <span class="a-action-btn" style="opacity:.4;cursor:not-allowed;"
                                                title="Already processed"><i class="ri-check-double-line"></i></span>
                                        @elseif ($row->stock_available)
                                            <a href="javascript:void(0)" class="a-action-btn a-success"
                                                title="Process Pre Order"
                                                onclick="processPreOrder({{ $row->item_id }})"><i
                                                    class="ri-play-circle-line"></i></a>
                                        @else
                                            <span class="a-action-btn a-danger" style="opacity:.5;cursor:not-allowed;"
                                                title="Insufficient stock"><i class="ri-error-warning-line"></i></span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13">
                                    <div class="a-empty">
                                        <div class="a-empty-icon"><i class="ri-inbox-line"></i></div>
                                        <h5>No pre-orders found</h5>
                                        <p>There are no pre-orders matching your current filters.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($preOrders->hasPages())
                <div class="a-card-footer">
                    {{ $preOrders->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('customJs')
    <script>
        function processPreOrder(itemId) {
            Swal.fire({
                title: 'Process this pre-order?',
                text: 'Stock will be deducted once and the order will move to processing.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d99f46',
                confirmButtonText: 'Yes, process it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (!result.isConfirmed) return;

                var url = '{{ route('admin.pre_orders.process', 'ID') }}'.replace('ID', itemId);

                $.ajax({
                    url: url,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Processed!',
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
                                title: 'Cannot process',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Something went wrong',
                            text: xhr.responseJSON?.message || 'Unable to process the pre-order.'
                        });
                    }
                });
            });
        }
    </script>
@endsection
