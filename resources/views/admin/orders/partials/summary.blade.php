@php
    $itemCount = $orderedItems->count();
    $statusLabels = [
        'pending' => ['label' => 'Pending', 'class' => 'a-badge-danger'],
        'confirm' => ['label' => 'Confirmed', 'class' => 'a-badge-info'],
        'shipped' => ['label' => 'Shipped', 'class' => 'a-badge-success'],
        'cancell' => ['label' => 'Cancelled', 'class' => 'a-badge-secondary'],
    ];
    $statusMeta = $statusLabels[$order->status] ?? ['label' => ucfirst($order->status), 'class' => 'a-badge-neutral'];
@endphp

<div class="a-card mb-3">
    <div class="a-card-body">
        <div class="order-summary-grid">
            <div>
                <div class="order-summary-label">Order</div>
                <div class="order-summary-value">#{{ $order->order_id }}</div>
                <div class="order-summary-sub">Placed {{ $order->created_at->format('d M Y, h:i A') }}</div>
            </div>

            <div>
                <div class="order-summary-label">Customer</div>
                <div class="order-summary-value">{{ $order->name }}</div>
                <div class="order-summary-sub"><span id="summary-item-count">{{ $itemCount }}</span> item(s)</div>
            </div>

            <div>
                <div class="order-summary-label">Grand Total</div>
                <div class="order-summary-value order-summary-total">৳ <span
                        id="summary-grand-total">{{ number_format($order->grand_total, 2) }}</span></div>
                <div class="order-summary-sub">Subtotal ৳ {{ number_format($order->subtotal, 2) }}</div>
            </div>

            <div class="order-summary-badges">
                <span class="a-badge {{ $statusMeta['class'] }}"><span class="dot"></span>{{ $statusMeta['label'] }}</span>
                <span
                    class="a-badge {{ $order->payment_status ? 'a-badge-success' : 'a-badge-warning' }}"><span
                        class="dot"></span>{{ $order->payment_status ? 'Paid' : 'Unpaid' }}</span>
            </div>
        </div>
    </div>
</div>
