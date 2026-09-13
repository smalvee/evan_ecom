<?php

namespace App\Services;

use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

/**
 * Weighted Average Cost (WAC) inventory costing.
 *
 * - recordPurchase(): increases stock and updates the weighted-average cost.
 * - applyReturn(): decreases stock and recalculates the average from history.
 * - recalculateAverageCost(): replays the full inventory history (purchases,
 *   supplier returns and stock-deducted sales) in chronological order to rebuild
 *   the average (used after a purchase edit / return).
 *
 * purchase_items.unit_cost remains the immutable historical cost of each purchase.
 */
class InventoryCostingService
{
    /**
     * Add purchased quantity and update the weighted-average cost.
     *
     * new_average = (existing_qty * existing_avg + purchase_qty * unit_cost)
     *             / (existing_qty + purchase_qty)
     */
    public function recordPurchase(ProductVariant $variant, float $qty, float $unitCost): void
    {
        DB::transaction(function () use ($variant, $qty, $unitCost) {
            $locked = ProductVariant::whereKey($variant->id)->lockForUpdate()->first();

            if (!$locked) {
                return;
            }

            $existingQty = max(0.0, (float) $locked->qty);
            $existingAvg = $this->toAmount($locked->average_cost ?? $locked->purchase_price);

            $newQty = $existingQty + $qty;
            $newAvg = $newQty > 0
                ? (($existingQty * $existingAvg) + ($qty * $unitCost)) / $newQty
                : $unitCost;

            $locked->qty = $newQty;
            $locked->average_cost = round($newAvg, 2);
            $locked->save();

            $variant->setAttribute('qty', $locked->qty);
            $variant->setAttribute('average_cost', $locked->average_cost);
        });
    }

    /**
     * Decrease stock for a supplier return and recalculate the average cost.
     */
    public function applyReturn(ProductVariant $variant, float $returnQty): void
    {
        DB::transaction(function () use ($variant, $returnQty) {
            $locked = ProductVariant::whereKey($variant->id)->lockForUpdate()->first();

            if (!$locked) {
                return;
            }

            $locked->qty = max(0.0, (float) $locked->qty - $returnQty);
            $locked->save();

            $this->recalculateAverageCost($locked);

            $variant->setAttribute('qty', $locked->qty);
            $variant->setAttribute('average_cost', $locked->average_cost);
        });
    }

    /**
     * Recalculate the current weighted-average cost by replaying the variant's
     * full inventory history in chronological order. Does not change the stock
     * quantity.
     *
     * Events:
     *   - purchase: add qty at its unit cost (blends into the running average).
     *   - return:   remove qty at the original purchase cost.
     *   - sale:     remove qty at the running average (sales leave the average
     *               unchanged, but reduce the quantity that weights later
     *               purchases, which is why they must be replayed).
     *
     * The final average is anchored to the variant's current on-hand quantity so
     * that stock value (qty x average_cost) equals the remaining inventory value.
     * Sales are only counted when stock actually left inventory (stock-deducted,
     * non-cancelled orders).
     */
    public function recalculateAverageCost(ProductVariant $variant): void
    {
        $locked = ProductVariant::whereKey($variant->id)->lockForUpdate()->first() ?? $variant;

        $events = [];

        // Purchases: add stock at its unit cost.
        foreach (DB::table('purchase_items')
            ->where('variant_id', $locked->id)
            ->select('id', 'date', 'qty', 'unit_cost')
            ->get() as $item) {
            $events[] = [
                'at' => ($item->date ?: '1970-01-01') . ' 00:00:00',
                'type' => 'purchase',
                'seq' => (int) $item->id,
                'qty' => (float) $item->qty,
                'cost' => $this->toAmount($item->unit_cost),
            ];
        }

        // Supplier returns: remove stock at the original purchase cost.
        foreach (DB::table('purchase_return_items as pri')
            ->join('purchase_returns as pr', 'pr.id', '=', 'pri.purchase_return_id')
            ->where('pri.variant_id', $locked->id)
            ->select('pri.id', 'pri.qty', 'pri.unit_cost', 'pr.created_at')
            ->get() as $item) {
            $events[] = [
                'at' => $item->created_at ?: '1970-01-01 00:00:00',
                'type' => 'return',
                'seq' => (int) $item->id,
                'qty' => (float) $item->qty,
                'cost' => $this->toAmount($item->unit_cost),
            ];
        }

        // Sales: remove stock at the running average. Only stock-deducted,
        // non-cancelled orders actually left inventory.
        foreach (DB::table('order_items as oi')
            ->join('orders as o', 'o.id', '=', 'oi.order_id')
            ->where('oi.product_id', $locked->id)
            ->where('o.stock_deducted', true)
            ->where('o.status', '!=', 'cancell')
            ->select('oi.id', 'oi.qty', 'o.created_at')
            ->get() as $item) {
            $events[] = [
                'at' => $item->created_at ?: '1970-01-01 00:00:00',
                'type' => 'sale',
                'seq' => (int) $item->id,
                'qty' => (float) $item->qty,
                'cost' => 0.0,
            ];
        }

        // Chronological replay. Purchases are applied before returns, which are
        // applied before sales when they share a timestamp.
        $rank = ['purchase' => 0, 'return' => 1, 'sale' => 2];
        usort($events, function ($a, $b) use ($rank) {
            return [$a['at'], $rank[$a['type']], $a['seq']]
                <=> [$b['at'], $rank[$b['type']], $b['seq']];
        });

        $qty = 0.0;
        $value = 0.0;

        foreach ($events as $event) {
            if ($event['type'] === 'purchase') {
                $qty += $event['qty'];
                $value += $event['qty'] * $event['cost'];
                continue;
            }

            // Never remove more than what is on hand at this point in history.
            $remove = min($event['qty'], $qty);

            if ($remove <= 0) {
                continue;
            }

            $unit = $event['type'] === 'return'
                ? $event['cost']
                : ($value / $qty);

            $value -= $remove * $unit;
            $qty -= $remove;
        }

        $onHand = max(0.0, (float) $locked->qty);

        // Only trust the replay when it accounts for stock; otherwise keep the
        // last known average (e.g. legacy opening stock with no purchase record).
        $avg = ($qty > 0 && $onHand > 0)
            ? max(0.0, $value) / $onHand
            : $this->toAmount($locked->average_cost ?? $locked->purchase_price);

        $locked->average_cost = round($avg, 2);
        $locked->save();

        $variant->setAttribute('average_cost', $locked->average_cost);
    }

    /**
     * Current inventory value = stock × average cost.
     */
    public function inventoryValue(ProductVariant $variant): float
    {
        $qty = max(0.0, (float) $variant->qty);
        $avg = $this->toAmount($variant->average_cost ?? $variant->purchase_price);

        return round($qty * $avg, 2);
    }

    protected function toAmount($value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        return round((float) $value, 2);
    }
}
