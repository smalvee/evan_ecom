<?php

namespace App\Services\Courier;

use App\Contracts\CourierInterface;
use App\Models\CourierSetting;
use App\Models\CourierShipment;
use App\Models\CourierStatusHistory;
use App\Models\Order;
use App\Support\CourierPricing;
use App\Support\CourierSanitizer;
use App\Support\CourierStatus;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Provider-independent courier entry point.
 *
 * The order system calls CourierManager only; the manager resolves the active
 * configuration, picks the driver (Mock vs Steadfast), enforces duplicate
 * protection and persists the local shipment + status history.
 */
class CourierManager
{
    protected ?CourierSetting $setting;

    public function __construct(?CourierSetting $setting = null)
    {
        $this->setting = $setting;
    }

    public static function make(?CourierSetting $setting = null): self
    {
        return new self($setting);
    }

    public function setting(): CourierSetting
    {
        return $this->setting ??= CourierSetting::current();
    }

    public function provider(): string
    {
        return $this->setting()->provider ?: config('courier.default', 'steadfast');
    }

    public function mode(): string
    {
        return $this->setting()->isLive() ? CourierSetting::MODE_LIVE : CourierSetting::MODE_TEST;
    }

    public function isLive(): bool
    {
        return $this->mode() === CourierSetting::MODE_LIVE;
    }

    public function isTest(): bool
    {
        return !$this->isLive();
    }

    public function enabled(): bool
    {
        return (bool) $this->setting()->is_active;
    }

    /**
     * Resolve the driver for the active environment.
     *
     * Live mode never silently falls back to the mock driver.
     *
     * @throws CourierConfigurationException
     */
    public function driver(): CourierInterface
    {
        $provider = $this->provider();

        if ($this->isLive()) {
            if (!$this->setting()->hasCredentials()) {
                throw new CourierConfigurationException('Live mode requires Steadfast API credentials.');
            }

            return match ($provider) {
                'steadfast' => new SteadfastCourier($this->setting(), config('courier.providers.steadfast', [])),
                default => throw new CourierConfigurationException("Unsupported courier provider [{$provider}]."),
            };
        }

        return match ($provider) {
            'steadfast' => new MockCourier($this->setting()),
            default => new MockCourier($this->setting()),
        };
    }

    public function testConnection(): CourierResponse
    {
        if ($this->isLive() && !$this->setting()->hasCredentials()) {
            return CourierResponse::failure('API credentials are required.');
        }

        try {
            return $this->driver()->testConnection();
        } catch (CourierConfigurationException $e) {
            return CourierResponse::failure($e->getMessage());
        } catch (Throwable $e) {
            return CourierResponse::failure('Unable to connect to the courier service.');
        }
    }

    /**
     * Submit an order to the courier.
     */
    public function createShipment(Order $order): CourierResponse
    {
        if (!$this->enabled()) {
            return CourierResponse::failure('Courier integration is disabled. Enable it in Courier Settings.');
        }

        if ($problem = $this->validateOrder($order)) {
            return CourierResponse::failure($problem);
        }

        // Live mode must never silently operate without credentials.
        if ($this->isLive() && !$this->setting()->hasCredentials()) {
            return CourierResponse::failure('Please configure Steadfast API credentials before using Live mode.');
        }

        // 1) Reserve the order's single active slot (duplicate + race safety).
        [$shipment, $duplicate] = $this->reserveShipment($order);

        if ($duplicate || !$shipment) {
            $shipment?->refresh();

            return CourierResponse::failure('This order has already been submitted to the courier.', [
                'consignment_id' => $shipment?->consignment_id,
                'tracking_code' => $shipment?->tracking_code,
                'status' => $shipment?->status,
            ]);
        }

        // 2) External request — deliberately outside any DB transaction.
        try {
            $response = $this->driver()->createShipment($order);
        } catch (CourierConfigurationException $e) {
            $shipment->delete();

            return CourierResponse::failure($e->getMessage());
        } catch (Throwable $e) {
            $shipment->delete();
            $this->log('create', $order, null, false, null, $e->getMessage());

            return CourierResponse::failure('Unable to reach the courier service. Please try again.');
        }

        if (!$response->success) {
            // Release the reservation so the order can be retried.
            $shipment->delete();
            $this->log('create', $order, null, false, $response->httpStatus, $response->message);

            return $response;
        }

        // 3) Persist the shipment + first history entry.
        $this->finalizeShipment($shipment, $response);
        $this->log('create', $order, $shipment, true, $response->httpStatus, $response->message);

        return $response;
    }

    public function cancelShipment(CourierShipment $shipment): CourierResponse
    {
        if (!$shipment->isActive()) {
            return CourierResponse::failure('This shipment is not active.');
        }

        if ($shipment->isDelivered()) {
            return CourierResponse::failure('Delivered shipments cannot be cancelled.');
        }

        try {
            $response = $this->driver()->cancelShipment($shipment);
        } catch (CourierConfigurationException $e) {
            return CourierResponse::failure($e->getMessage());
        } catch (Throwable $e) {
            $this->log('cancel', $shipment->order, $shipment, false, null, $e->getMessage());

            return CourierResponse::failure('Unable to reach the courier service. Please try again.');
        }

        if (!$response->success) {
            return $response;
        }

        DB::transaction(function () use ($shipment, $response) {
            $shipment->status = 'cancelled';
            $shipment->active = null;
            $shipment->response_data = CourierSanitizer::clean($response->raw);
            $shipment->save();

            $this->recordStatus($shipment, 'cancelled', $response->message, $response->raw);
        });

        $this->log('cancel', $shipment->order, $shipment, true, $response->httpStatus, $response->message);

        return $response;
    }

    /**
     * Release a shipment locally without contacting the provider.
     *
     * Use when the consignment no longer exists at the courier (e.g. deleted in
     * their portal). It frees the order's active slot so it can be re-sent.
     */
    public function releaseShipment(CourierShipment $shipment): CourierResponse
    {
        if (!$shipment->isActive()) {
            return CourierResponse::failure('This shipment is not active.');
        }

        DB::transaction(function () use ($shipment) {
            $shipment->status = 'cancelled';
            $shipment->active = null;
            $shipment->save();

            $this->recordStatus(
                $shipment,
                'cancelled',
                'Released locally — consignment is no longer active at the courier.',
                ['released_locally' => true]
            );
        });

        $this->log('release', $shipment->order, $shipment, true, null, 'Released locally.');

        return CourierResponse::success('Shipment released. You can send this order to the courier again.');
    }

    public function refreshStatus(CourierShipment $shipment): CourierResponse
    {
        try {
            $response = $this->driver()->getStatus($shipment);
        } catch (CourierConfigurationException $e) {
            return CourierResponse::failure($e->getMessage());
        } catch (Throwable $e) {
            return CourierResponse::failure('Unable to reach the courier service. Please try again.');
        }

        if (!$response->success) {
            return $response;
        }

        $newStatus = $response->data['status'] ?? $shipment->status;
        $changed = $newStatus !== $shipment->status;

        DB::transaction(function () use ($shipment, $response, $newStatus, $changed) {
            if (!$changed) {
                return;
            }

            $shipment->status = $newStatus;
            if ($newStatus === 'cancelled') {
                $shipment->active = null;
            }
            $shipment->response_data = CourierSanitizer::clean($response->raw);
            $shipment->save();

            $this->recordStatus($shipment, $newStatus, $response->message, $response->raw);
        });

        if ($changed) {
            $this->log('status', $shipment->order, $shipment, true, $response->httpStatus, $response->message);
        }

        return $response;
    }

    /**
     * Test-mode only: force a courier status (drives the status history).
     */
    public function simulateStatus(CourierShipment $shipment, string $status): CourierResponse
    {
        if ($this->isLive()) {
            return CourierResponse::failure('Simulation is only available in Test mode.');
        }

        if (!in_array($status, CourierStatus::SIMULATABLE, true)) {
            return CourierResponse::failure('Unknown courier status.');
        }

        DB::transaction(function () use ($shipment, $status) {
            $shipment->status = $status;
            if ($status === 'cancelled') {
                $shipment->active = null;
            }
            $shipment->save();

            $this->recordStatus($shipment, $status, 'Simulated status update (Test mode).', [
                'mock' => true,
                'delivery_status' => $status,
            ]);
        });

        return CourierResponse::success('Courier status simulated.', ['status' => $status]);
    }

    protected function validateOrder(Order $order): ?string
    {
        // Only confirmed orders may be dispatched to the courier.
        if ($order->status !== 'confirm') {
            return 'Only confirmed orders can be sent to the courier.';
        }

        if (blank($order->name)) {
            return 'Order is missing a recipient name.';
        }

        if (blank($order->phone)) {
            return 'Order is missing a recipient phone number.';
        }

        if (blank($order->address)) {
            return 'Order is missing a delivery address.';
        }

        $order->loadMissing('items');

        if ($order->items->isEmpty()) {
            return 'Order has no items to ship.';
        }

        return null;
    }

    /**
     * Create the active shipment placeholder inside a transaction, locking the
     * order row and relying on the (order_id, active) unique index for races.
     *
     * @return array{0: ?CourierShipment, 1: bool} [shipment, isDuplicate]
     */
    protected function reserveShipment(Order $order): array
    {
        try {
            return DB::transaction(function () use ($order) {
                Order::whereKey($order->getKey())->lockForUpdate()->first();

                $existing = CourierShipment::query()
                    ->where('order_id', $order->id)
                    ->where('active', 1)
                    ->first();

                if ($existing) {
                    return [$existing, true];
                }

                $shipment = CourierShipment::create([
                    'order_id' => $order->id,
                    'provider' => $this->provider(),
                    'mode' => $this->mode(),
                    'invoice' => (string) ($order->order_id ?: $order->id),
                    'status' => 'creating',
                    'cod_amount' => CourierPricing::codAmount($order),
                    'delivery_type' => 'home',
                    'recipient_name' => $order->name,
                    'recipient_phone' => $order->phone,
                    'recipient_address' => $order->address,
                    'item_description' => CourierPricing::itemDescription($order),
                    'total_lot' => CourierPricing::totalLot($order),
                    'active' => 1,
                ]);

                return [$shipment, false];
            });
        } catch (QueryException $e) {
            // Unique (order_id, active) violation: a concurrent request won.
            $existing = CourierShipment::query()
                ->where('order_id', $order->id)
                ->where('active', 1)
                ->first();

            return [$existing, true];
        }
    }

    protected function finalizeShipment(CourierShipment $shipment, CourierResponse $response): void
    {
        DB::transaction(function () use ($shipment, $response) {
            $data = $response->data;
            $status = $data['status'] ?? 'in_review';

            $shipment->fill([
                'consignment_id' => $data['consignment_id'] ?? $shipment->consignment_id,
                'invoice' => $data['invoice'] ?? $shipment->invoice,
                'tracking_code' => $data['tracking_code'] ?? $shipment->tracking_code,
                'status' => $status,
                'response_data' => CourierSanitizer::clean($response->raw),
            ])->save();

            $this->recordStatus($shipment, $status, $response->message, $response->raw);
        });
    }

    protected function recordStatus(CourierShipment $shipment, string $status, ?string $message, array $raw = []): void
    {
        CourierStatusHistory::create([
            'courier_shipment_id' => $shipment->id,
            'status' => $status,
            'message' => $message,
            'raw_response' => CourierSanitizer::clean($raw) ?: null,
        ]);
    }

    /**
     * Structured log entry. Never includes credentials.
     */
    protected function log(
        string $operation,
        ?Order $order,
        ?CourierShipment $shipment,
        bool $success,
        ?int $httpStatus,
        ?string $message
    ): void {
        Log::info('courier.operation', [
            'provider' => $this->provider(),
            'mode' => $this->mode(),
            'operation' => $operation,
            'order_id' => $order?->id,
            'courier_shipment_id' => $shipment?->id,
            'success' => $success,
            'http_status' => $httpStatus,
            'message' => $message,
        ]);
    }
}
