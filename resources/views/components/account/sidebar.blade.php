@php
    $user = auth()->user();

    $links = [
        [
            'label' => 'Dashboard',
            'route' => 'account.userDashboard',
            'match' => ['account.userDashboard'],
            'icon' => 'grid',
        ],
        [
            'label' => 'My Orders',
            'route' => 'account.orders',
            'match' => ['account.orders', 'account.orderDetails'],
            'icon' => 'shopping-bag',
        ],
        [
            'label' => 'Profile',
            'route' => 'account.profile',
            'match' => ['account.profile', 'account.profileUpdate', 'account.updatePassword'],
            'icon' => 'user',
        ],
    ];
@endphp

<div class="account-user">
    <div class="account-avatar" aria-hidden="true">{{ mb_substr($user->name ?? 'U', 0, 1) }}</div>
    <div class="account-user-meta">
        <span class="account-user-hello">Hello,</span>
        <strong>{{ $user->name ?? 'Customer' }}</strong>
    </div>
</div>

<nav class="account-nav" aria-label="Account navigation">
    @foreach ($links as $link)
        @php $isActive = request()->routeIs(...$link['match']); @endphp
        <a href="{{ route($link['route']) }}"
            class="account-nav-link {{ $isActive ? 'active' : '' }}"
            @if ($isActive) aria-current="page" @endif>
            <i data-feather="{{ $link['icon'] }}" aria-hidden="true"></i>
            <span>{{ $link['label'] }}</span>
        </a>
    @endforeach

    <a href="{{ route('account.logout') }}" class="account-nav-link account-nav-logout">
        <i data-feather="log-out" aria-hidden="true"></i>
        <span>Logout</span>
    </a>
</nav>
