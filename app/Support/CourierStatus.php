<?php

namespace App\Support;

/**
 * Provider-independent courier status labels + admin badge classes.
 *
 * Kept deliberately separate from the ecommerce order status. Courier status
 * must never be written onto `orders.status` without an explicit mapping.
 */
class CourierStatus
{
    public const LABELS = [
        'creating' => 'Submitting',
        'in_review' => 'In Review',
        'pending' => 'Pending',
        'delivered' => 'Delivered',
        'partial_delivered' => 'Partially Delivered',
        'cancelled' => 'Cancelled',
        'hold' => 'Hold',
        'unknown' => 'Unknown',
    ];

    public const CLASSES = [
        'creating' => 'a-badge-neutral',
        'in_review' => 'a-badge-info',
        'pending' => 'a-badge-warning',
        'delivered' => 'a-badge-success',
        'partial_delivered' => 'a-badge-success',
        'cancelled' => 'a-badge-danger',
        'hold' => 'a-badge-warning',
        'unknown' => 'a-badge-secondary',
    ];

    /**
     * Statuses an admin/developer may simulate in Test mode.
     */
    public const SIMULATABLE = [
        'in_review',
        'pending',
        'delivered',
        'partial_delivered',
        'cancelled',
        'hold',
        'unknown',
    ];

    public static function label(?string $status): string
    {
        if ($status === null || $status === '') {
            return 'Unknown';
        }

        return self::LABELS[$status] ?? ucfirst(str_replace('_', ' ', $status));
    }

    public static function badgeClass(?string $status): string
    {
        return self::CLASSES[$status] ?? 'a-badge-secondary';
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return self::LABELS;
    }
}
