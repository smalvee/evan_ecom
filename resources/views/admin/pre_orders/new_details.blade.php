@extends('admin.layouts.new_app')

@section('content')
    @php
        $state = $item->pre_order_status === 'pending'
            ? ($stockAvailable ? 'available' : 'pending')
            : $item->pre_order_status;
    @endphp

    <div class="container-fluid">
        <div class="a-page-head">
            <div class="a-page-head-text">
                <ul class="a-breadcrumb">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('admin.pre_orders.index') }}">Pre Orders</a></li>
                    <li class="is-active">#{{ $order->order_id }}</li>
                </ul>
                <h4 class="a-page-title">
                    Pre Order: {{ $order->order_id }}
                    <span class="a-badge a-badge-warning" style="vertical-align:middle;"><span
                            class="dot"></span>PRE ORDER</span>
                </h4>
                <p class="a-page-desc">Review and process this pre-order once stock is available.</p>
            </div>
            <div class="a-actions">
                <button type="button" class="btn btn-outline-secondary"
                    onclick="location.href='{{ route('admin.pre_orders.index') }}'">
                    <i class="ri-arrow-left-line"></i> Back
                </button>
                <button type="button" class="btn btn-theme"
                    onclick="location.href='{{ route('orders.details', $order->id) }}'">
                    <i class="ri-file-list-3-line"></i> Full Order
                </button>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-7">
                <div class="a-card mb-3">
                    <div class="a-card-head">
                        <h5>Pre-Order Item</h5>
                    </div>
                    <div class="a-card-body">
                        <div class="table-responsive">
                            <table class="table all-package theme-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Variant</th>
                                        <th class="text-end">Qty</th>
                                        <th class="text-end">Unit Price</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="a-cell-main">{{ $variant->product->name ?? '—' }}</td>
                                        <td>{{ $variant->sku ?? $item->name }}</td>
                                        <td class="text-end">{{ (int) $item->qty }}</td>
                                        <td class="text-end">৳ {{ number_format((float) $item->price, 2) }}</td>
                                        <td class="text-end">৳ {{ number_format((float) $item->total, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex flex-wrap align-items-center gap-3 mt-3">
                            <div>
                                <span class="text-muted">Current Stock:</span>
                                <strong>{{ $stock === null ? 'Untracked' : (int) $stock }}</strong>
                            </div>
                            <div>
                                <span class="text-muted">Required:</span>
                                <strong>{{ (int) $item->qty }}</strong>
                            </div>
                            <div>
                                <span class="text-muted">Pre-order Status:</span>
                                @if ($state == 'pending')
                                    <span class="a-badge a-badge-danger"><span class="dot"></span>Pending</span>
                                @elseif ($state == 'available')
                                    <span class="a-badge a-badge-success"><span class="dot"></span>Stock Available</span>
                                @elseif ($state == 'processing')
                                    <span class="a-badge a-badge-info"><span class="dot"></span>Processing</span>
                                @elseif ($state == 'completed')
                                    <span class="a-badge a-badge-success"><span class="dot"></span>Completed</span>
                                @elseif ($state == 'cancelled')
                                    <span class="a-badge a-badge-secondary"><span class="dot"></span>Cancelled</span>
                                @else
                                    <span class="a-badge a-badge-neutral"><span
                                            class="dot"></span>{{ ucfirst($state) }}</span>
                                @endif
                            </div>
                        </div>

                        @if ($state === 'pending' && !$stockAvailable)
                            <div class="alert alert-danger mt-3 mb-0">
                                <strong>Insufficient stock.</strong>
                                Current stock: {{ (int) $stock }}. Required: {{ (int) $item->qty }}. Process the
                                pre-order once enough stock is received.
                            </div>
                        @elseif ($state === 'available' || $state === 'pending')
                            <button type="button" class="btn btn-theme mt-3" onclick="processPreOrder()">
                                <i class="ri-play-circle-line"></i> Process Pre Order
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="a-card mb-3">
                    <div class="a-card-head">
                        <h5>Customer</h5>
                    </div>
                    <div class="a-card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2 d-flex justify-content-between">
                                <span class="text-muted">Name</span>
                                <strong>{{ $order->name }}</strong>
                            </li>
                            <li class="mb-2 d-flex justify-content-between">
                                <span class="text-muted">Phone</span>
                                <strong>{{ $order->phone }}</strong>
                            </li>
                            <li class="d-flex justify-content-between">
                                <span class="text-muted">Address</span>
                                <strong class="text-end" style="max-width:60%;">{{ $order->address }}</strong>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="a-card">
                    <div class="a-card-head">
                        <h5>Order</h5>
                    </div>
                    <div class="a-card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2 d-flex justify-content-between">
                                <span class="text-muted">Order #</span>
                                <strong>{{ $order->order_id }}</strong>
                            </li>
                            <li class="mb-2 d-flex justify-content-between">
                                <span class="text-muted">Date</span>
                                <strong>{{ $order->created_at->format('d M Y, h:i A') }}</strong>
                            </li>
                            <li class="mb-2 d-flex justify-content-between align-items-center">
                                <span class="text-muted">Order Status</span>
                                @if ($order->status == 'pending')
                                    <span class="a-badge a-badge-danger"><span class="dot"></span>Pending</span>
                                @elseif ($order->status == 'confirm')
                                    <span class="a-badge a-badge-info"><span class="dot"></span>Confirmed</span>
                                @elseif ($order->status == 'shipped')
                                    <span class="a-badge a-badge-success"><span class="dot"></span>Delivered</span>
                                @elseif ($order->status == 'cancell')
                                    <span class="a-badge a-badge-secondary"><span class="dot"></span>Cancelled</span>
                                @endif
                            </li>
                            <li class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Payment</span>
                                <span
                                    class="a-badge {{ $order->payment_status ? 'a-badge-success' : 'a-badge-warning' }}">
                                    <span class="dot"></span>{{ $order->payment_status ? 'Paid' : 'Unpaid' }}
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customJs')
    <script>
        function processPreOrder() {
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

                var url = '{{ route('admin.pre_orders.process', $item->id) }}';

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
