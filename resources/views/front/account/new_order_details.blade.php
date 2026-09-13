@extends('front.layouts.new_app')

@section('content')
    <section style="background:#d99f46;color:#fff;" class="py-3">
        <div class="container text-center">
            <h1 class="fw-bold mb-1">Order Details</h1>
        </div>
    </section>
    <section><br>
        <div class="container">
            <!-- Order Summary -->
            <div class="card mb-4">
                <div class="card-header" style="background:#d99f46;color:#fff;">
                    <h5 class="mb-0">Order #{{ $order->id }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <strong>Date:</strong> {{ $order->created_at->format('d M Y, h:i A') }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Status:</strong>
                            @if ($order->status == 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($order->status == 'completed')
                                <span class="badge bg-success">Completed</span>
                            @elseif($order->status == 'cancelled')
                                <span class="badge bg-danger">Cancelled</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                            @endif
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Total Amount:</strong> Tk: {{ number_format($order->grand_total, 2) }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Shipping Address:</strong> {{ $order->address ?? 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card">
                <div class="card-header" style="background:#d99f46;color:#fff;">
                    <h5 class="mb-0">Order Items</h5>
                </div>
                <div class="card-body">
                    @if (empty($orderedItems))
                        <p>No items found in this order.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orderedItems as $item)
                                        <tr>
                                            <td>{{ $item->name ?? 'N/A' }}
                                                @if ($item->is_pre_order)
                                                    <span class="badge bg-warning text-dark">Pre Order</span>
                                                @endif
                                            </td>
                                            <td>{{ $item->qty }}</td>
                                            <td>Tk: {{ number_format($item->price, 2) }}</td>
                                            <td>Tk: {{ number_format($item->price * $item->qty, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="3" class="text-end">Subtotal:</th>
                                        <th>Tk: {{ number_format($order->subtotal, 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-end">Shipping:</th>
                                        <th>Tk: {{ number_format($order->shipping, 2) }}</th>
                                    </tr>
                                    @if ($order->discount != 0)
                                        <tr>
                                            <th colspan="3" class="text-end">Discount:</th>
                                            <th>Tk: {{ number_format($order->discount, 2) }}</th>
                                        </tr>
                                    @endif

                                    <tr>
                                        <th colspan="3" class="text-end">Grand Total:</th>
                                        <th>Tk: {{ number_format($order->grand_total, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Back Button -->
            <div class="mt-3">
                <a href="{{ route('account.userDashboard') }}" class="btn" style="background:#d99f46;color:#fff;">← Back to Dashboard</a>
            </div>
        </div>
    </section>

@endsection

@section('customJs')
@endsection
