<?php

use Gloudemans\Shoppingcart\Facades\Cart;

?>

<ul class="cart-list">
    @if (!empty($cartContent) && count($cartContent) > 0)
        @foreach ($cartContent as $item)
            @php
                $product_image = $item->options->productImage ?? '';
                $product_info = App\Models\NewProduct::find($item->id);
            @endphp
            <li class="product-box-contain">
                <div class="drop-cart">
                    <a href="product-left-thumbnail.html" class="drop-image">
                        @if ($product_image)
                            <img src="{{ asset('uploads/products/small/' . $product_image) }}" class="blur-up lazyload"
                                alt="">
                        @endif
                    </a>
                    <div class="drop-contain">
                        <a href="product-left-thumbnail.html">
                            <h5>{{ $product_info->name ?? 'Product' }}</h5>
                        </a>
                        <h6><span>{{ $item->qty }} x</span> {{ $item->price }} Tk</h6>
                        <button class="close-button close_button" id="closeButton" data-rowid="{{ $item->rowId }}">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>
            </li>
        @endforeach
    @else
        <li class="text-center">Your cart is empty.</li>
    @endif
</ul>

<div class="price-box">
    <h5>Total :</h5>
    <h4 class="theme-color fw-bold">{{ Cart::subtotal() }} Tk</h4>
</div>

<div class="button-group">
    <a href="cart.html" class="btn btn-sm cart-button">View Cart</a>
    <a href="checkout.html" class="btn btn-sm cart-button theme-bg-color text-white">Checkout</a>
</div>
