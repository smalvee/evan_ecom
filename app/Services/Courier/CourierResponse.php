<?php

namespace App\Services\Courier;

/**
 * Normalised result returned by every courier driver.
 *
 * Shape:
 * [
 *   'success' => bool,
 *   'message' => string,
 *   'data' => array,        // provider-independent fields
 *   'raw_response' => array // sanitised provider payload (never secrets)
 * ]
 */
class CourierResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message,
        public readonly array $data = [],
        public readonly array $raw = [],
        public readonly ?int $httpStatus = null,
    ) {
    }

    public static function success(string $message, array $data = [], array $raw = [], ?int $httpStatus = 200): self
    {
        return new self(true, $message, $data, $raw, $httpStatus);
    }

    public static function failure(string $message, array $data = [], array $raw = [], ?int $httpStatus = null): self
    {
        return new self(false, $message, $data, $raw, $httpStatus);
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
            'raw_response' => $this->raw,
        ];
    }

    public function consignmentId(): ?string
    {
        $id = $this->data['consignment_id'] ?? null;

        return $id === null ? null : (string) $id;
    }

    public function status(): ?string
    {
        return $this->data['status'] ?? null;
    }
}
