<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Historical record of a manual stock adjustment on a product variant.
 *
 * `quantity` is the signed adjustment applied to `product_variants.qty`
 * (increase => positive, decrease => negative, correction => difference).
 */
class ProductAdjustment extends Model
{
    use HasFactory;

    public const TYPE_INCREASE = 'increase';
    public const TYPE_DECREASE = 'decrease';
    public const TYPE_CORRECTION = 'correction';

    public const REASONS = [
        'Stock Correction',
        'Damaged',
        'Lost',
        'Found Stock',
        'Other',
    ];

    protected $fillable = [
        'adjustment_no',
        'product_id',
        'variant_id',
        'adjustment_type',
        'quantity',
        'stock_before',
        'stock_after',
        'actual_stock',
        'reason',
        'note',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'stock_before' => 'integer',
        'stock_after' => 'integer',
        'actual_stock' => 'integer',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function product()
    {
        return $this->belongsTo(NewProduct::class, 'product_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return array<string, string>
     */
    public static function types(): array
    {
        return [
            self::TYPE_INCREASE => 'Increase Stock',
            self::TYPE_DECREASE => 'Decrease Stock',
            self::TYPE_CORRECTION => 'Stock Correction',
        ];
    }

    public function typeLabel(): string
    {
        return self::types()[$this->adjustment_type] ?? ucfirst((string) $this->adjustment_type);
    }

    public function isIncrease(): bool
    {
        return $this->adjustment_type === self::TYPE_INCREASE;
    }

    public function isDecrease(): bool
    {
        return $this->adjustment_type === self::TYPE_DECREASE;
    }

    public function isCorrection(): bool
    {
        return $this->adjustment_type === self::TYPE_CORRECTION;
    }

    /**
     * Signed adjustment formatted with an explicit + / - sign.
     */
    public function signedQuantity(): string
    {
        return ($this->quantity >= 0 ? '+' : '') . $this->quantity;
    }
}
