<div class="a-card mb-3">
    <div class="a-card-head">
        <h5>Payment</h5>
    </div>
    <div class="a-card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-muted">Payment Status</span>
            <span id="payment-badge"
                class="a-badge {{ $order->payment_status ? 'a-badge-success' : 'a-badge-warning' }}">
                <span class="dot"></span>{{ $order->payment_status ? 'Paid' : 'Unpaid' }}
            </span>
        </div>

        <button type="button" class="btn btn-outline-secondary w-100" id="paymentToggleBtn">
            <i class="ri-refresh-line"></i>
            <span id="paymentToggleLabel">{{ $order->payment_status ? 'Mark as Unpaid' : 'Mark as Paid' }}</span>
        </button>
    </div>
</div>

<div class="a-card mb-3">
    <div class="a-card-head">
        <h5>Financial Summary</h5>
    </div>
    <div class="a-card-body">
        <div class="fin-row">
            <span>Subtotal</span>
            <span>৳ <span id="subtotal-text">{{ number_format($order->subtotal, 2) }}</span></span>
        </div>
        <div class="fin-row">
            <span>Shipping</span>
            <span>৳ <span id="shipping-text">{{ number_format($order->shipping, 2) }}</span></span>
        </div>
        <div class="fin-row">
            <span>Coupon Discount</span>
            <span>- ৳ <span id="coupon-text">{{ number_format($order->discount, 2) }}</span></span>
        </div>
        <div class="fin-total">
            <span>Grand Total</span>
            <span>৳ <span id="grand-total-text">{{ number_format($order->grand_total, 2) }}</span></span>
        </div>
    </div>
</div>
