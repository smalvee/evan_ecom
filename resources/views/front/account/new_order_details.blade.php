@extends('front.account.layout')

@section('account-title', 'Order Details')
@section('account-heading', 'Order #' . ($order->order_id ?: $order->id))
@section('account-subheading', 'Placed on ' . (optional($order->created_at)->format('F d, Y') ?? '—'))

@section('account-content')
    <div class="account-card">
        <div class="account-card-head">
            <h2>Order Status</h2>
            <x-account.order-status :status="$order->status" />
        </div>
        <div class="account-card-body">
            <dl class="account-info">
                <div class="account-info-row">
                    <dt>Order Number</dt>
                    <dd>#{{ $order->order_id ?: $order->id }}</dd>
                </div>
                <div class="account-info-row">
                    <dt>Order Date</dt>
                    <dd>{{ optional($order->created_at)->format('d M Y, h:i A') ?? '—' }}</dd>
                </div>
                <div class="account-info-row">
                    <dt>Payment</dt>
                    <dd>
                        <span
                            class="order-status {{ $order->payment_status ? 'order-status--shipped' : 'order-status--pending' }}">
                            {{ $order->payment_status ? 'Paid' : 'Unpaid' }}
                        </span>
                    </dd>
                </div>
                @if (!empty($order->coupon_code))
                    <div class="account-info-row">
                        <dt>Coupon</dt>
                        <dd>{{ $order->coupon_code }}</dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>

    <div class="account-card">
        <div class="account-card-head">
            <h2>Products</h2>
        </div>
        <div class="account-card-body">
            @if ($orderedItems->isEmpty())
                <div class="account-empty">
                    <div class="account-empty-icon"><i data-feather="package" aria-hidden="true"></i></div>
                    <h3>No items found</h3>
                    <p>This order has no items.</p>
                </div>
            @else
                <div class="order-items">
                    @foreach ($orderedItems as $item)
                        <x-account.order-item :item="$item" />
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="account-card h-100">
                <div class="account-card-head">
                    <h2>Order Summary</h2>
                </div>
                <div class="account-card-body">
                    <div class="account-summary">
                        <div class="account-summary-row">
                            <span>Subtotal</span>
                            <span class="value">&#2547;{{ number_format((float) $order->subtotal, 2) }}</span>
                        </div>
                        <div class="account-summary-row">
                            <span>Delivery</span>
                            <span class="value">
                                @if ((float) $order->shipping > 0)
                                    &#2547;{{ number_format((float) $order->shipping, 2) }}
                                @else
                                    Free
                                @endif
                            </span>
                        </div>
                        @if ((float) $order->discount != 0)
                            <div class="account-summary-row is-discount">
                                <span>Discount</span>
                                <span class="value">-&#2547;{{ number_format((float) $order->discount, 2) }}</span>
                            </div>
                        @endif
                        <div class="account-summary-row is-total">
                            <span>Total</span>
                            <span class="value">&#2547;{{ number_format((float) $order->grand_total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="account-card account-shipping h-100">
                <div class="account-card-head">
                    <h2>Shipping Information</h2>
                </div>
                <div class="account-card-body">
                    <dl>
                        <div>
                            <dt>Name</dt>
                            <dd>{{ $order->name ?: 'Not provided' }}</dd>
                        </div>
                        <div>
                            <dt>Phone</dt>
                            <dd>{{ $order->phone ?: 'Not provided' }}</dd>
                        </div>
                        <div>
                            <dt>Address</dt>
                            <dd>{!! nl2br(e($order->address ?: 'Not provided')) !!}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('account.orders') }}" class="account-btn account-btn-outline">
            <i data-feather="arrow-left" aria-hidden="true"></i>
            Back to Orders
        </a>
    </div>
@endsection
