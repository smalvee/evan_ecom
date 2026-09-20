<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderSuccessPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_success_page_renders_order_id_and_invoice_link(): void
    {
        $orderId = '20260016';

        $response = $this->get(route('front.thankyou', $orderId));

        $response->assertOk();
        $response->assertSee('Order Success');
        $response->assertSee('Your order has been placed successfully');
        $response->assertSee($orderId);
        $response->assertSee(route('front.invoice', $orderId), false);
        $response->assertSee(route('front.home'), false);
        $response->assertSee('Continue Shopping');
        $response->assertSee('View Invoice');
    }
}
