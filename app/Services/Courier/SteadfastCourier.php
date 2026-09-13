<?php

namespace App\Services\Courier;

use App\Contracts\CourierInterface;
use App\Models\CourierSetting;
use App\Models\CourierShipment;
use App\Models\Order;
use App\Support\CourierPricing;
use App\Support\CourierSanitizer;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * The ONLY place that knows Steadfast-specific endpoints, headers, payload
 * names and response structures.
 *
 * Endpoints follow the Steadfast v1 API (portal.steadfast.com.bd/api/v1):
 *   POST /create_order
 *   GET  /status_by_cid/{consignment_id}
 *   GET  /status_by_trackingcode/{tracking_code}
 *   GET  /get_balance
 */
class SteadfastCourier implements CourierInterface
{
    public function __construct(
        protected CourierSetting $setting,
        protected array $config = [],
    ) {
    }

    public function provider(): string
    {
        return 'steadfast';
    }

    public function mode(): string
    {
        return $this->setting->isLive() ? CourierSetting::MODE_LIVE : CourierSetting::MODE_TEST;
    }

    public function createShipment(Order $order): CourierResponse
    {
        if (!$this->setting->hasCredentials()) {
            return CourierResponse::failure('API credentials are required.');
        }

        return $this->send('POST', '/create_order', $this->buildPayload($order), 'create');
    }

    public function cancelShipment(CourierShipment $shipment): CourierResponse
    {
        // The Steadfast v1 API exposes no consignment-cancellation endpoint.
        // Do not invent one — cancellation is performed in the Steadfast portal.
        return CourierResponse::failure(
            'Steadfast does not expose a cancellation API. Please cancel the consignment from the Steadfast portal.'
        );
    }

    public function getStatus(CourierShipment $shipment): CourierResponse
    {
        if (!$this->setting->hasCredentials()) {
            return CourierResponse::failure('API credentials are required.');
        }

        if (filled($shipment->consignment_id)) {
            return $this->send('GET', '/status_by_cid/' . rawurlencode((string) $shipment->consignment_id), [], 'status');
        }

        if (filled($shipment->tracking_code)) {
            return $this->send('GET', '/status_by_trackingcode/' . rawurlencode((string) $shipment->tracking_code), [], 'status');
        }

        return CourierResponse::failure('This shipment has no consignment or tracking code to query.');
    }

    public function testConnection(): CourierResponse
    {
        if (!$this->setting->hasCredentials()) {
            return CourierResponse::failure('API credentials are required.');
        }

        // Lightweight authenticated endpoint used purely to validate credentials.
        return $this->send('GET', '/get_balance', [], 'test_connection');
    }

    protected function buildPayload(Order $order): array
    {
        $payload = [
            'invoice' => (string) ($order->order_id ?: $order->id),
            'recipient_name' => (string) $order->name,
            'recipient_phone' => (string) $order->phone,
            'recipient_address' => (string) $order->address,
            'cod_amount' => CourierPricing::codAmount($order),
            'item_description' => CourierPricing::itemDescription($order),
            'total_lot' => CourierPricing::totalLot($order),
            'delivery_type' => 'home',
        ];

        // Only send optional fields that actually exist on the order.
        if (filled($order->notes)) {
            $payload['note'] = (string) $order->notes;
        }

        return $payload;
    }

    protected function client(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl())
            ->acceptJson()
            ->asJson()
            ->withHeaders([
                'Api-Key' => (string) $this->setting->api_key,
                'Secret-Key' => (string) $this->setting->secret_key,
            ])
            ->timeout((int) config('courier.timeout', 15))
            ->connectTimeout((int) config('courier.connect_timeout', 8));
    }

    protected function baseUrl(): string
    {
        $url = $this->config['base_url']
            ?? config('courier.providers.steadfast.base_url');

        return rtrim((string) $url, '/');
    }

    protected function send(string $method, string $path, array $payload, string $operation): CourierResponse
    {
        try {
            $response = $method === 'POST'
                ? $this->client()->post($path, $payload)
                : $this->client()->get($path);
        } catch (ConnectionException $e) {
            return CourierResponse::failure('Unable to connect to Steadfast. Please try again.');
        } catch (\Throwable $e) {
            return CourierResponse::failure('Unable to reach Steadfast. Please try again.');
        }

        return $this->interpret($response, $operation);
    }

    protected function interpret(Response $response, string $operation): CourierResponse
    {
        $status = $response->status();
        $json = $response->json();

        if (!is_array($json)) {
            return CourierResponse::failure('Steadfast returned an invalid response.', [], [], $status);
        }

        $raw = CourierSanitizer::clean($json);

        if (in_array($status, [401, 403], true)) {
            return CourierResponse::failure('Invalid Steadfast credentials.', [], $raw, $status);
        }

        if ($status === 404) {
            return CourierResponse::failure($json['message'] ?? 'Consignment not found.', [], $raw, $status);
        }

        if ($status === 429) {
            return CourierResponse::failure('Steadfast rate limit reached. Please try again shortly.', [], $raw, $status);
        }

        if ($status === 422) {
            return CourierResponse::failure($this->validationMessage($json), [], $raw, $status);
        }

        if ($status >= 500) {
            return CourierResponse::failure('Steadfast is currently unavailable. Please try again.', [], $raw, $status);
        }

        if (!$response->successful()) {
            return CourierResponse::failure($json['message'] ?? 'Steadfast request failed.', [], $raw, $status);
        }

        // Steadfast also returns an internal `status` code in the body.
        if (isset($json['status']) && (int) $json['status'] !== 200) {
            return CourierResponse::failure($json['message'] ?? 'Steadfast rejected the request.', [], $raw, $status);
        }

        if ($operation === 'test_connection') {
            return CourierResponse::success(
                'Live connection successful.',
                ['provider' => 'steadfast', 'mode' => 'live'],
                $raw,
                $status
            );
        }

        if ($operation === 'status') {
            $delivery = $json['delivery_status']
                ?? ($json['consignment']['status'] ?? null);

            if (!$delivery) {
                return CourierResponse::failure('Steadfast did not return a status.', [], $raw, $status);
            }

            return CourierResponse::success('Status retrieved from Steadfast.', ['status' => $delivery], $raw, $status);
        }

        // create
        $consignment = is_array($json['consignment'] ?? null) ? $json['consignment'] : [];

        if (empty($consignment['consignment_id'])) {
            return CourierResponse::failure('Steadfast returned an unexpected response.', [], $raw, $status);
        }

        return CourierResponse::success(
            $json['message'] ?? 'Shipment created successfully.',
            [
                'consignment_id' => (string) $consignment['consignment_id'],
                'invoice' => $consignment['invoice'] ?? null,
                'tracking_code' => $consignment['tracking_code'] ?? null,
                'status' => $consignment['status'] ?? 'in_review',
            ],
            $raw,
            $status
        );
    }

    protected function validationMessage(array $json): string
    {
        $message = $json['message'] ?? 'Courier rejected the shipment.';

        if (!empty($json['errors']) && is_array($json['errors'])) {
            $first = collect($json['errors'])->flatten()->first();

            if ($first) {
                $message .= ' ' . $first;
            }
        }

        return $message;
    }
}
