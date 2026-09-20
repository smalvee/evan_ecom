<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\CourierShipment;
use App\Models\Order;
use App\Services\Courier\CourierManager;
use App\Support\CourierStatus;
use Illuminate\Http\Request;

class CourierShipmentController extends Controller
{
    public function create(Order $order)
    {
        $order->loadMissing('items');

        $response = CourierManager::make()->createShipment($order);

        return $this->json($response->toArray());
    }

    public function refresh(Order $order)
    {
        $shipment = $this->latestShipment($order);

        if (!$shipment) {
            return $this->json(['success' => false, 'message' => 'This order has no courier shipment yet.']);
        }

        return $this->json(CourierManager::make()->refreshStatus($shipment)->toArray());
    }

    public function cancel(Order $order)
    {
        $shipment = $this->latestShipment($order);

        if (!$shipment) {
            return $this->json(['success' => false, 'message' => 'This order has no courier shipment yet.']);
        }

        return $this->json(CourierManager::make()->cancelShipment($shipment)->toArray());
    }

    /**
     * Release the shipment locally (no provider call) so the order can be
     * re-sent after the consignment was removed at the courier.
     */
    public function release(Order $order)
    {
        $shipment = $this->latestShipment($order);

        if (!$shipment) {
            return $this->json(['success' => false, 'message' => 'This order has no courier shipment yet.']);
        }

        return $this->json(CourierManager::make()->releaseShipment($shipment)->toArray());
    }

    /**
     * Test-mode only: simulate a courier status change.
     */
    public function simulate(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:' . implode(',', CourierStatus::SIMULATABLE),
        ]);

        $shipment = $this->latestShipment($order);

        if (!$shipment) {
            return $this->json(['success' => false, 'message' => 'This order has no courier shipment yet.']);
        }

        return $this->json(CourierManager::make()->simulateStatus($shipment, $validated['status'])->toArray());
    }

    protected function latestShipment(Order $order): ?CourierShipment
    {
        return CourierShipment::query()
            ->where('order_id', $order->id)
            ->latest('id')
            ->first();
    }

    protected function json(array $payload)
    {
        return response()->json($payload);
    }
}
