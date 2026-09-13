@props([
    'label',
    'value' => 0,
    'href' => null,
    'cta' => 'View',
    'icon' => 'package',
    'tone' => 'default',
])

<div class="account-stat-card account-stat-card--{{ $tone }}">
    <div class="account-stat-top">
        <span class="account-stat-icon" aria-hidden="true"><i data-feather="{{ $icon }}"></i></span>
        <span class="account-stat-label">{{ $label }}</span>
    </div>
    <div class="account-stat-value">{{ $value }}</div>
    @if ($href)
        <a href="{{ $href }}" class="account-stat-link">
            {{ $cta }}
            <i data-feather="arrow-right" aria-hidden="true"></i>
        </a>
    @endif
</div>
