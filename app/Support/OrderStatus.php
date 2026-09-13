<?php

namespace App\Support;

/**
 * Single source of truth for customer-facing order status labels/styles.
 *
 * The database stores raw magic strings (`pending`, `confirm`, `shipped`,
 * `cancell` — note the typo). Those values must never be shown to customers.
 */
class OrderStatus
{
    public const LABELS = [
        'pending' => 'Pending',
        'confirm' => 'Confirmed',
        'shipped' => 'Shipped',
        'cancell' => 'Cancelled',
    ];

    /**
     * Semantic tone per status (maps to `.order-status--{key}` CSS).
     */
    public const KEYS = [
        'pending' => 'pending',
        'confirm' => 'confirmed',
        'shipped' => 'shipped',
        'cancell' => 'cancelled',
    ];

    public static function label(?string $status): string
    {
        if ($status === null || $status === '') {
            return 'Unknown';
        }

        // Never leak raw database values to customers.
        return self::LABELS[$status] ?? 'Unknown';
    }

    public static function key(?string $status): string
    {
        return self::KEYS[$status] ?? 'unknown';
    }

    /**
     * Valid statuses, keyed by the raw database value.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return self::LABELS;
    }
}
