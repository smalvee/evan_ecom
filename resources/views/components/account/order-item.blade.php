@props(['item'])

@php
    $image = $item->image;
    $imageUrl = $image && !empty($image->image)
        ? asset('uploads/products/thumb/' . $image->image)
        : asset('admin-assets/img/default-150x150.png');

    $productName = $item->variant?->product?->name ?? $item->name ?? 'Product';
    $qty = (int) $item->qty;
    $price = (float) $item->price;
    $total = (float) ($item->total ?? $price * $qty);
@endphp

<div class="order-item">
    <img src="{{ $imageUrl }}" alt="{{ $productName }}" class="order-item-img" loading="lazy"
        onerror="this.onerror=null;this.src='{{ asset('admin-assets/img/default-150x150.png') }}';">

    <div class="order-item-body">
        <div class="order-item-name">{{ $productName }}</div>
        <div class="order-item-meta">
            @if ($item->name && $item->name !== $productName)
                <span>SKU: {{ $item->name }}</span>
            @endif
            @if ($item->is_pre_order)
                <span class="order-status order-status--preorder">Pre Order</span>
            @endif
        </div>
        <div class="order-item-qty">{{ $qty }} &times; &#2547;{{ number_format($price, 2) }}</div>
    </div>

    <div class="order-item-total">&#2547;{{ number_format($total, 2) }}</div>
</div>
