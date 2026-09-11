@php
    $cards = $cards ?? [];
@endphp

<div class="row g-3 mb-3">
    @foreach ($cards as $c)
        <div class="col-sm-6 col-xxl-3 col-lg-6">
            <div class="a-stat-card d-flex justify-content-between">
                <div>
                    <div class="a-stat-label">
                        {{ $c['label'] }}
                        @if (!empty($c['hint']))
                            <i class="ri-information-line text-muted" style="cursor: help;" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="{{ $c['hint'] }}"></i>
                        @endif
                    </div>
                    <div class="a-stat-value">{{ $c['value'] }}</div>
                    @if (!empty($c['sub']))
                        <span class="a-stat-delta {{ $c['delta'] ?? 'flat' }}">{{ $c['sub'] }}</span>
                    @endif
                </div>
                @if (!empty($c['icon']))
                    <div class="a-stat-icon"
                        style="background: var(--a-{{ $c['color'] ?? 'primary' }}-soft); color: var(--a-{{ $c['color'] ?? 'primary' }});">
                        <i class="{{ $c['icon'] }}"></i>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>
