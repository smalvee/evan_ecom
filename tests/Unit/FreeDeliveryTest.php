<?php

namespace Tests\Unit;

use App\Models\Order;
use Tests\TestCase;

class FreeDeliveryTest extends TestCase
{
    public function test_all_free_delivery_items_returns_true()
    {
        $items = collect([
            (object) ['options' => collect(['freeDelivery' => 1])],
            (object) ['options' => collect(['freeDelivery' => 1])],
        ]);

        $this->assertTrue(Order::isFreeDeliveryCart($items));
    }

    public function test_mixed_items_returns_false()
    {
        $items = collect([
            (object) ['options' => collect(['freeDelivery' => 1])],
            (object) ['options' => collect(['freeDelivery' => 0])],
        ]);

        $this->assertFalse(Order::isFreeDeliveryCart($items));
    }

    public function test_all_normal_items_returns_false()
    {
        $items = collect([
            (object) ['options' => collect(['freeDelivery' => 0])],
            (object) ['options' => collect(['freeDelivery' => 0])],
        ]);

        $this->assertFalse(Order::isFreeDeliveryCart($items));
    }

    public function test_empty_cart_returns_false()
    {
        $this->assertFalse(Order::isFreeDeliveryCart(collect()));
    }
}
