@php
    $statusSteps = ['pending' => 1, 'confirm' => 2, 'shipped' => 3];
    $step = $statusSteps[$order->status] ?? 0;
    $isCancelled = $order->status === 'cancell';
@endphp

<div class="a-card mb-3">
    <div class="a-card-head">
        <h5>Order Status</h5>
        @if ($isCancelled)
            <span class="a-badge a-badge-secondary"><span class="dot"></span>Cancelled</span>
        @elseif ($order->status === 'pending')
            <span class="a-badge a-badge-danger"><span class="dot"></span>Pending</span>
        @elseif ($order->status === 'confirm')
            <span class="a-badge a-badge-info"><span class="dot"></span>Confirmed</span>
        @elseif ($order->status === 'shipped')
            <span class="a-badge a-badge-success"><span class="dot"></span>Shipped</span>
        @endif
    </div>
    <div class="a-card-body">
        @if ($isCancelled)
            <div class="order-cancelled-note">
                <i class="ri-error-warning-line"></i>
                <span>This order is cancelled. Stock was restored if it had been deducted.</span>
            </div>
        @else
            <ol class="order-timeline" aria-label="Order status progress">
                <li class="{{ $step >= 1 ? 'is-done' : '' }} {{ $order->status === 'pending' ? 'is-active' : '' }}">
                    <span class="tl-dot"><i class="ri-time-line" aria-hidden="true"></i></span>
                    <span class="tl-label">Pending</span>
                </li>
                <li class="{{ $step >= 2 ? 'is-done' : '' }} {{ $order->status === 'confirm' ? 'is-active' : '' }}">
                    <span class="tl-dot"><i class="ri-check-double-line" aria-hidden="true"></i></span>
                    <span class="tl-label">Confirmed</span>
                </li>
                <li class="{{ $step >= 3 ? 'is-done' : '' }} {{ $order->status === 'shipped' ? 'is-active' : '' }}">
                    <span class="tl-dot"><i class="ri-truck-line" aria-hidden="true"></i></span>
                    <span class="tl-label">Shipped</span>
                </li>
            </ol>
        @endif

        <div class="mt-3">
            <label class="form-label" for="status">Change Status</label>
            <select name="status" id="status" class="form-select">
                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirm" {{ $order->status == 'confirm' ? 'selected' : '' }}>Confirmed</option>
                <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                <option value="cancell" {{ $order->status == 'cancell' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <p class="text-muted small mb-0 mt-2">
                Stock is deducted when an order becomes <strong>Confirmed</strong> or <strong>Shipped</strong> and
                restored when an order is <strong>Cancelled</strong>.
            </p>
        </div>
    </div>
</div>
