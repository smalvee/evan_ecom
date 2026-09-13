@extends('front.account.layout')

@section('account-title', 'My Orders')
@section('account-heading', 'My Orders')
@section('account-subheading', 'Track and review every order you have placed.')

@section('account-content')
    <div class="account-card">
        <div class="account-card-head">
            <h2>Order History</h2>
            <div class="account-filters">
                <a href="{{ route('account.orders') }}"
                    class="account-filter {{ $activeStatus === null ? 'active' : '' }}">All</a>
                @foreach (\App\Support\OrderStatus::options() as $value => $label)
                    <a href="{{ route('account.orders', ['status' => $value]) }}"
                        class="account-filter {{ $activeStatus === $value ? 'active' : '' }}">{{ $label }}</a>
                @endforeach
            </div>
        </div>

        <div class="account-card-body">
            @if ($orders->isEmpty())
                <div class="account-empty">
                    <div class="account-empty-icon"><i data-feather="shopping-bag" aria-hidden="true"></i></div>
                    <h3>No orders found</h3>
                    <p>
                        @if ($activeStatus)
                            There are no orders with this status.
                        @else
                            You haven't placed any orders yet.
                        @endif
                    </p>
                    <a href="{{ route('front.home') }}" class="account-btn account-btn-primary">Start Shopping</a>
                </div>
            @else
                {{-- Desktop table --}}
                <div class="account-table-wrap d-none d-md-block">
                    <table class="account-table">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td><strong>#{{ $order->order_id ?: $order->id }}</strong></td>
                                    <td>{{ optional($order->created_at)->format('d M Y') }}</td>
                                    <td><x-account.order-status :status="$order->status" /></td>
                                    <td class="col-total">&#2547;{{ number_format((float) $order->grand_total, 2) }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('account.orderDetails', $order->id) }}"
                                            class="account-btn account-btn-outline">View Details</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile cards --}}
                <div class="d-md-none">
                    @foreach ($orders as $order)
                        <div class="order-card">
                            <div class="order-card-head">
                                <span class="order-ref">#{{ $order->order_id ?: $order->id }}</span>
                                <x-account.order-status :status="$order->status" />
                            </div>
                            <div class="order-card-grid">
                                <div>
                                    <span class="label">Date</span>
                                    <span class="value">{{ optional($order->created_at)->format('d M Y') }}</span>
                                </div>
                                <div>
                                    <span class="label">Total</span>
                                    <span class="value">&#2547;{{ number_format((float) $order->grand_total, 2) }}</span>
                                </div>
                            </div>
                            <a href="{{ route('account.orderDetails', $order->id) }}"
                                class="account-btn account-btn-primary w-100">View Details</a>
                        </div>
                    @endforeach
                </div>

                {{ $orders->links() }}
            @endif
        </div>
    </div>
@endsection
