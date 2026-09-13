<div class="a-page-head">
    <div class="a-page-head-text">
        <ul class="a-breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('orders.index') }}">Orders</a></li>
            <li class="is-active">#{{ $order->order_id }}</li>
        </ul>
        <h4 class="a-page-title">Order #{{ $order->order_id }}</h4>
        <p class="a-page-desc">Manage customer, items, delivery, payment and order status</p>
    </div>
    <div class="a-actions">
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
            <i class="ri-arrow-left-line"></i> Back to Orders
        </a>
        <a href="{{ route('front.invoice', $order->order_id) }}" class="btn btn-theme">
            <i class="ri-file-list-3-line"></i> Invoice
        </a>
    </div>
</div>
