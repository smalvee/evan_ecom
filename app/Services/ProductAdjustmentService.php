<?php

namespace App\Services;

use App\Models\ProductAdjustment;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Applies manual stock adjustments to `product_variants.qty`.
 *
 * The database is always the source of truth: the current stock is read (and
 * locked) inside the transaction, so frontend-supplied values for
 * current_stock / stock_before / stock_after / adjustment are ignored.
 */
class ProductAdjustmentService
{
    /**
     * @param  array{variant_id:int, adjustment_type:string, quantity?:int|null, actual_stock?:int|null, reason?:string|null, note?:string|null}  $data
     */
    public function adjust(array $data, ?int $userId = null): ProductAdjustment
    {
        return DB::transaction(function () use ($data, $userId) {
            $variant = ProductVariant::with('product')
                ->lockForUpdate()
                ->find($data['variant_id']);

            if (!$variant) {
                throw ValidationException::withMessages([
                    'variant_id' => 'The selected product variant was not found.',
                ]);
            }

            $type = $data['adjustment_type'];
            $stockBefore = (int) $variant->qty;
            $actualStock = null;

            switch ($type) {
                case ProductAdjustment::TYPE_INCREASE:
                    $quantity = (int) ($data['quantity'] ?? 0);

                    if ($quantity < 1) {
                        throw ValidationException::withMessages([
                            'quantity' => 'Increase quantity must be at least 1.',
                        ]);
                    }

                    $adjustment = $quantity;
                    $stockAfter = $stockBefore + $quantity;
                    break;

                case ProductAdjustment::TYPE_DECREASE:
                    $quantity = (int) ($data['quantity'] ?? 0);

                    if ($quantity < 1) {
                        throw ValidationException::withMessages([
                            'quantity' => 'Decrease quantity must be at least 1.',
                        ]);
                    }

                    if ($quantity > $stockBefore) {
                        throw ValidationException::withMessages([
                            'quantity' => 'Decrease quantity cannot exceed the current stock (' . $stockBefore . ').',
                        ]);
                    }

                    $adjustment = -$quantity;
                    $stockAfter = $stockBefore - $quantity;
                    break;

                case ProductAdjustment::TYPE_CORRECTION:
                    $actualStock = (int) ($data['actual_stock'] ?? -1);

                    if ($actualStock < 0) {
                        throw ValidationException::withMessages([
                            'actual_stock' => 'Actual physical stock cannot be negative.',
                        ]);
                    }

                    $stockAfter = $actualStock;
                    $adjustment = $stockAfter - $stockBefore;
                    break;

                default:
                    throw ValidationException::withMessages([
                        'adjustment_type' => 'Invalid adjustment type.',
                    ]);
            }

            // Apply the new stock to the existing stock field.
            $variant->qty = $stockAfter;
            $variant->save();

            $adjustmentRecord = ProductAdjustment::create([
                'adjustment_no' => null,
                'product_id' => $variant->product_id,
                'variant_id' => $variant->id,
                'adjustment_type' => $type,
                'quantity' => $adjustment,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'actual_stock' => $actualStock,
                'reason' => $data['reason'] ?? null,
                'note' => $data['note'] ?? null,
                'created_by' => $userId,
            ]);

            // Human-readable reference derived from the auto-increment id.
            $adjustmentRecord->adjustment_no = 'ADJ-' . str_pad((string) $adjustmentRecord->id, 6, '0', STR_PAD_LEFT);
            $adjustmentRecord->save();

            return $adjustmentRecord;
        });
    }
}
