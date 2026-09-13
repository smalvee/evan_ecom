<?php

namespace App\Services;

use App\Models\ProductVariant;
use App\Models\ProductVariantPriceHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Central pricing logic for product variants.
 *
 * Separates:
 *   - purchase cost  (product_variants.purchase_price)  -> accounting/COGS
 *   - current pricing (compare_price = MRP, selling_price) -> commercial
 *
 * Every real price change is written to product_variant_price_histories.
 */
class ProductVariantPricingService
{
    /**
     * Manual admin price update (MRP + selling price) with history.
     *
     * @return array{changed: bool, old_compare: float, old_selling: float}
     *
     * @throws ValidationException
     */
    public function updatePrices(ProductVariant $variant, $comparePrice, $sellingPrice, string $reason, ?int $userId = null): array
    {
        $compare = $this->toAmount($comparePrice);
        $selling = $this->toAmount($sellingPrice);

        $this->validate($compare, $selling);

        $oldCompare = $this->toAmount($variant->compare_price);
        $oldSelling = $this->toAmount($variant->selling_price);

        if ($this->same($oldCompare, $compare) && $this->same($oldSelling, $selling)) {
            return ['changed' => false, 'old_compare' => $oldCompare, 'old_selling' => $oldSelling];
        }

        DB::transaction(function () use ($variant, $compare, $selling, $oldCompare, $oldSelling, $reason, $userId) {
            $variant->compare_price = $compare;
            $variant->selling_price = $selling;
            // Lock the price so later purchase edits don't overwrite it.
            $variant->price_manually_managed = true;
            $variant->save();

            $this->recordHistory($variant, $oldCompare, $compare, $oldSelling, $selling, $reason, $userId);
        });

        return ['changed' => true, 'old_compare' => $oldCompare, 'old_selling' => $oldSelling];
    }

    /**
     * Apply only the purchase cost to a variant (no pricing change).
     *
     * Used when editing a purchase without the explicit "update current price"
     * option, so current MRP / selling price are never touched.
     */
    public function applyPurchaseCost(ProductVariant $variant, float $unitCost): void
    {
        DB::transaction(function () use ($variant, $unitCost) {
            $variant->purchase_price = round($unitCost, 2);
            $variant->save();
        });
    }

    /**
     * Apply purchase cost to a variant. Unless the price is manually managed
     * (or $force is true), also (re)calculate the initial/current MRP and
     * selling price.
     *
     * - Purchase creation: $force = false -> establishes initial pricing, but a
     *   manually managed price is preserved.
     * - Purchase edit with "Update Current Product Price" checked: $force = true
     *   -> intentionally recalculates the current price and records history.
     */
    public function applyPurchasePricing(
        ProductVariant $variant,
        float $unitCost,
        float $profitMargin,
        float $discount,
        string $reason,
        ?int $userId = null,
        bool $force = false
    ): void {
        DB::transaction(function () use ($variant, $unitCost, $profitMargin, $discount, $reason, $userId, $force) {
            $variant->purchase_price = round($unitCost, 2);

            if ($force || !$variant->price_manually_managed) {
                $compare = round($unitCost + $profitMargin, 2);
                $selling = round(max(0, $unitCost + $profitMargin - $discount), 2);

                $oldCompare = $this->toAmount($variant->compare_price);
                $oldSelling = $this->toAmount($variant->selling_price);

                $variant->compare_price = $compare;
                $variant->selling_price = $selling;
                $variant->save();

                if (!$this->same($oldCompare, $compare) || !$this->same($oldSelling, $selling)) {
                    $this->recordHistory($variant, $oldCompare, $compare, $oldSelling, $selling, $reason, $userId);
                }
            } else {
                $variant->save();
            }
        });
    }

    /**
     * @throws ValidationException
     */
    protected function validate(float $compare, float $selling): void
    {
        $errors = [];

        if ($compare < 0) {
            $errors['compare_price'] = 'MRP must be 0 or greater.';
        }

        if ($selling < 0) {
            $errors['selling_price'] = 'Selling price must be 0 or greater.';
        } elseif ($selling > $compare) {
            $errors['selling_price'] = 'Selling price cannot be greater than the MRP.';
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    protected function recordHistory(
        ProductVariant $variant,
        float $oldCompare,
        float $newCompare,
        float $oldSelling,
        float $newSelling,
        ?string $reason,
        ?int $userId
    ): void {
        ProductVariantPriceHistory::create([
            'product_variant_id' => $variant->id,
            'old_compare_price' => $oldCompare,
            'new_compare_price' => $newCompare,
            'old_selling_price' => $oldSelling,
            'new_selling_price' => $newSelling,
            'reason' => $reason,
            'changed_by' => $userId,
        ]);
    }

    /**
     * Convert a (possibly VARCHAR) value to a rounded float without relying on
     * lexical string comparison.
     */
    protected function toAmount($value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        return round((float) $value, 2);
    }

    protected function same(float $a, float $b): bool
    {
        return abs($a - $b) < 0.0001;
    }
}
