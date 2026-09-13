<?php

namespace App\Services\Courier;

use App\Contracts\CourierInterface;
use App\Models\CourierSetting;
use App\Models\CourierShipment;
use App\Models\Order;
use App\Support\CourierStatus;

/**
 * Fully functional stand-in for a real courier provider.
 *
 * Used whenever the active environment is Test/Mock. It never performs an
 * external HTTP request, yet produces data shaped exactly like the real
 * adapter so the rest of the application cannot tell the difference.
 */
class MockCourier implements CourierInterface
{
    public function __construct(protected CourierSetting $setting)
    {
    }

    public function provider(): string
    {
        return $this->setting->provider ?: 'steadfast';
    }

    public function mode(): string
    {
        return CourierSetting::MODE_TEST;
    }

    public function createShipment(Order $order): CourierResponse
    {
        $sequence = ((int) CourierShipment::query()->max('id')) + 100001;

        $data = [
            'consignment_id' => 'TEST-' . $sequence,
            'invoice' => 'TEST-ORDER-' . ($order->order_id ?: $order->id),
            'tracking_code' => 'TEST-TRK-' . $sequence,
            'status' => 'in_review',
        ];

        return CourierResponse::success(
            'Mock shipment created successfully.',
            $data,
            ['mock' => true, 'consignment' => $data],
            200
        );
    }

    public function cancelShipment(CourierShipment $shipment): CourierResponse
    {
        if ($shipment->isCancelled()) {
            return CourierResponse::failure('This shipment is already cancelled.');
        }

        if ($shipment->isDelivered()) {
            return CourierResponse::failure('Delivered shipments cannot be cancelled.');
        }

        return CourierResponse::success(
            'Mock shipment cancelled.',
            ['status' => 'cancelled'],
            ['mock' => true, 'delivery_status' => 'cancelled'],
            200
        );
    }

    public function getStatus(CourierShipment $shipment): CourierResponse
    {
        $status = $shipment->status ?: 'unknown';

        return CourierResponse::success(
            'Mock status retrieved.',
            ['status' => $status],
            ['mock' => true, 'delivery_status' => $status],
            200
        );
    }

    public function testConnection(): CourierResponse
    {
        return CourierResponse::success(
            'Test connection successful. Mock courier environment is active.',
            ['mode' => 'test', 'provider' => $this->provider()],
            ['mock' => true],
            200
        );
    }

    public function supportsSimulation(): bool
    {
        return true;
    }

    public static function statuses(): array
    {
        return CourierStatus::SIMULATABLE;
    }
}
