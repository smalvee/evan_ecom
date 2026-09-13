@props(['status'])

@php
    $key = \App\Support\OrderStatus::key($status);
    $label = \App\Support\OrderStatus::label($status);
@endphp

<span class="order-status order-status--{{ $key }}">{{ $label }}</span>
