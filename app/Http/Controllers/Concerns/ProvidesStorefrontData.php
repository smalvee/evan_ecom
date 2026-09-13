<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Category;
use Gloudemans\Shoppingcart\Facades\Cart;

/**
 * The storefront layout (header nav + cart sidebar) expects `$categories`
 * and `$cartContent` on every page. Centralised here so account controllers
 * don't repeat the same two lines.
 */
trait ProvidesStorefrontData
{
    protected function storefrontData(array $data = []): array
    {
        return array_merge([
            'categories' => Category::latest('id')->get(),
            'cartContent' => Cart::content(),
        ], $data);
    }
}
