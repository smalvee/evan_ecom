<?php

namespace App\Contracts;

use App\Models\CourierShipment;
use App\Models\Order;
use App\Services\Courier\CourierResponse;

/**
 * Provider-independent courier contract.
 *
 * The order system talks to CourierManager only; individual drivers (Mock,
 * Steadfast, and future Pathao/RedX adapters) implement this interface.
 */
interface CourierInterface
{
    /**
     * Provider key, e.g. "steadfast".
     */
    public function provider(): string;

    /**
     * "test" or "live".
     */
    public function mode(): string;

    /**
     * Submit an order to the courier and return a normalised response.
     */
    public function createShipment(Order $order): CourierResponse;

    /**
     * Cancel an existing shipment where the provider supports it.
     */
    public function cancelShipment(CourierShipment $shipment): CourierResponse;

    /**
     * Fetch the latest status for a shipment.
     */
    public function getStatus(CourierShipment $shipment): CourierResponse;

    /**
     * Verify connectivity/credentials for the active environment.
     */
    public function testConnection(): CourierResponse;
}
