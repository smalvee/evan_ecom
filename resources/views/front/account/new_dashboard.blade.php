@extends('front.account.layout')

@section('account-title', 'Dashboard')
@section('account-heading', 'My Account')
@section('account-subheading', 'Welcome back, ' . $user->name . '!')

@section('account-content')
    <div class="account-stat-grid">
        <x-account.stat-card label="Total Orders" :value="$stats['total']" icon="package"
            :href="route('account.orders')" cta="View orders" />

        <x-account.stat-card label="Pending" :value="$stats['pending']" icon="clock" tone="warning"
            :href="route('account.orders', ['status' => 'pending'])" cta="View pending" />

        <x-account.stat-card label="Confirmed" :value="$stats['confirmed']" icon="check-circle" tone="info"
            :href="route('account.orders', ['status' => 'confirm'])" cta="View confirmed" />

        <x-account.stat-card label="Shipped" :value="$stats['shipped']" icon="truck" tone="success"
            :href="route('account.orders', ['status' => 'shipped'])" cta="View shipped" />
    </div>

    <div class="account-card">
        <div class="account-card-head">
            <h2>Personal Information</h2>
            <a href="{{ route('account.profile') }}" class="account-btn account-btn-outline">Edit Profile</a>
        </div>
        <div class="account-card-body">
            <dl class="account-info">
                <div class="account-info-row">
                    <dt>Name</dt>
                    <dd>{{ $user->name ?: 'Not provided' }}</dd>
                </div>
                <div class="account-info-row">
                    <dt>Email</dt>
                    <dd>{{ $user->email ?: 'Not provided' }}</dd>
                </div>
                <div class="account-info-row">
                    <dt>Phone</dt>
                    <dd>{{ $user->phone ?: 'Not provided' }}</dd>
                </div>
                <div class="account-info-row">
                    <dt>Member since</dt>
                    <dd>{{ optional($user->created_at)->format('F Y') ?? '—' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="account-card">
        <div class="account-card-head">
            <h2>Recent Orders</h2>
            @if ($stats['total'] > 0)
                <a href="{{ route('account.orders') }}" class="account-link">
                    View All Orders
                    <i data-feather="arrow-right" aria-hidden="true"></i>
                </a>
            @endif
        </div>
        <div class="account-card-body">
            @forelse ($recentOrders as $order)
                <div class="order-row">
                    <div class="order-ref">#{{ $order->order_id ?: $order->id }}</div>
                    <div class="order-date">{{ optional($order->created_at)->format('d M Y') }}</div>
                    <div><x-account.order-status :status="$order->status" /></div>
                    <div class="order-total">&#2547;{{ number_format((float) $order->grand_total, 2) }}</div>
                    <div class="order-action">
                        <a href="{{ route('account.orderDetails', $order->id) }}"
                            class="account-btn account-btn-outline">View</a>
                    </div>
                </div>
            @empty
                <div class="account-empty">
                    <div class="account-empty-icon"><i data-feather="shopping-bag" aria-hidden="true"></i></div>
                    <h3>No orders yet</h3>
                    <p>You haven't placed any orders yet.</p>
                    <a href="{{ route('front.home') }}" class="account-btn account-btn-primary">Start Shopping</a>
                </div>
            @endforelse
        </div>
    </div>
@endsection
