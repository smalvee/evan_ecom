<?php

namespace App\Support;

use App\Models\Order;

/**
 * Derives courier payload values from authoritative order data.
 *
 * COD is always calculated server-side from the stored order; browser input is
 * never trusted. Existing pricing fields (subtotal/shipping/discount/
 * grand_total/payment_status) are used as-is.
 */
class CourierPricing
{
    /**
     * Cash the courier must collect. Already-paid orders collect nothing.
     */
    public static function codAmount(Order $order): float
    {
        if ((int) $order->payment_status === 1) {
            return 0.0;
        }

        return max(0.0, round((float) $order->grand_total, 2));
    }

    public static function itemDescription(Order $order): string
    {
        $order->loadMissing('items');

        $description = $order->items->map(function ($item) {
            $name = $item->name ?: ('Item #' . $item->product_id);

            return $name . ' x' . (int) $item->qty;
        })->implode(', ');

        return mb_substr($description, 0, 255);
    }

    public static function totalLot(Order $order): int
    {
        $order->loadMissing('items');

        return (int) $order->items->sum(fn ($item) => (int) $item->qty);
    }
}
