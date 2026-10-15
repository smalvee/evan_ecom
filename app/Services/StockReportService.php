<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Stock Report (inventory valuation by period).
 *
 * For each product variant the report replays its full inventory ledger in
 * chronological order using the application's Weighted Average Cost (WAC)
 * method — the same method used by InventoryCostingService — and snapshots the
 * state at the start and end of the selected period.
 *
 * Ledger events (transaction types that actually move inventory):
 *   - purchase   : +qty at unit_cost
 *   - return     : -qty at original purchase cost (supplier/purchase return)
 *   - sale       : -qty at the running average cost (stock-deducted,
 *                  non-cancelled orders only)
 *   - adjustment : +/-qty valued at the running average cost
 *
 * The four displayed groups reconcile as:
 *   Closing = Opening + Purchase - Sales
 * where the "Purchase" group is net stock-in (purchases - returns + positive
 * adjustments) and "Sales" is net stock-out (sales + negative adjustments).
 *
 * A legacy opening-balance seed is injected for stock that exists on a variant
 * but has no recorded purchase (e.g. pre-migration stock), valued at the
 * variant's current average/purchase cost, so closing quantity always
 * reconciles to the current on-hand quantity.
 */
class StockReportService
{
    public function variantsQuery(array $filters)
    {
        $q = DB::table('product_variants as pv')
            ->leftJoin('new_products as np', 'np.id', '=', 'pv.product_id')
            ->leftJoin('categories as c', 'c.id', '=', 'np.cat_id')
            ->leftJoin('brands as b', 'b.id', '=', 'np.brand_id');

        if (!empty($filters['search'])) {
            $q->where(function ($x) use ($filters) {
                $x->where('np.name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('pv.sku', 'like', '%' . $filters['search'] . '%');
            });
        }
        if (!empty($filters['category'])) {
            $q->where('np.cat_id', $filters['category']);
        }
        if (!empty($filters['brand'])) {
            $q->where('np.brand_id', $filters['brand']);
        }

        return $q;
    }

    public function rows(?Carbon $from, ?Carbon $to, array $filters = [], int $perPage = 20)
    {
        $variants = $this->variantsQuery($filters)
            ->selectRaw('
                pv.id, pv.sku, pv.qty, pv.purchase_price, pv.average_cost,
                COALESCE(np.name, pv.sku) as product,
                c.name as category, b.name as brand
            ')
            ->orderBy('pv.id')
            ->paginate($perPage)
            ->appends(request()->query());

        $ids = collect($variants->items())->pluck('id')->all();

        if (empty($ids)) {
            return $variants;
        }

        $events = $this->loadEvents($ids);

        foreach ($variants as $variant) {
            $variant->stock = $this->compute($variant, $events[$variant->id] ?? [], $from, $to);
        }

        return $variants;
    }

    protected function loadEvents(array $ids): array
    {
        $events = [];
        $rank = ['purchase' => 0, 'return' => 1, 'sale' => 2, 'adjustment' => 3];

        // Purchases.
        foreach (DB::table('purchase_items')
            ->whereIn('variant_id', $ids)
            ->select('id', 'variant_id', 'date', 'qty', 'unit_cost')
            ->get() as $r) {
            $events[$r->variant_id][] = [
                'at' => $r->date ?: '1970-01-01',
                'rank' => $rank['purchase'],
                'seq' => (int) $r->id,
                'type' => 'purchase',
                'qty' => (float) $r->qty,
                'cost' => $this->toAmount($r->unit_cost),
            ];
        }

        // Supplier / purchase returns.
        foreach (DB::table('purchase_return_items as pri')
            ->join('purchase_returns as pr', 'pr.id', '=', 'pri.purchase_return_id')
            ->whereIn('pri.variant_id', $ids)
            ->select('pri.id', 'pri.variant_id', 'pri.qty', 'pri.unit_cost', 'pr.created_at')
            ->get() as $r) {
            $events[$r->variant_id][] = [
                'at' => $r->created_at ?: '1970-01-01 00:00:00',
                'rank' => $rank['return'],
                'seq' => (int) $r->id,
                'type' => 'return',
                'qty' => (float) $r->qty,
                'cost' => $this->toAmount($r->unit_cost),
            ];
        }

        // Sales (only stock that actually left inventory).
        foreach (DB::table('order_items as oi')
            ->join('orders as o', 'o.id', '=', 'oi.order_id')
            ->whereIn('oi.product_id', $ids)
            ->where('o.stock_deducted', true)
            ->where('o.status', '!=', 'cancell')
            ->select('oi.id', 'oi.product_id as variant_id', 'oi.qty', 'o.created_at')
            ->get() as $r) {
            $events[$r->variant_id][] = [
                'at' => $r->created_at ?: '1970-01-01 00:00:00',
                'rank' => $rank['sale'],
                'seq' => (int) $r->id,
                'type' => 'sale',
                'qty' => (float) $r->qty,
                'cost' => 0.0,
            ];
        }

        // Manual stock adjustments (signed quantity).
        foreach (DB::table('product_adjustments')
            ->whereIn('variant_id', $ids)
            ->select('id', 'variant_id', 'quantity', 'created_at')
            ->get() as $r) {
            $events[$r->variant_id][] = [
                'at' => $r->created_at ?: '1970-01-01 00:00:00',
                'rank' => $rank['adjustment'],
                'seq' => (int) $r->id,
                'type' => 'adjustment',
                'qty' => (float) $r->quantity,
                'cost' => 0.0,
            ];
        }

        return $events;
    }

    protected function compute($variant, array $events, ?Carbon $from, ?Carbon $to): array
    {
        usort($events, function ($a, $b) {
            return [$a['at'], $a['rank'], $a['seq']] <=> [$b['at'], $b['rank'], $b['seq']];
        });

        // Reconcile to the current on-hand quantity: any quantity not explained
        // by the ledger is a legacy opening balance (pre-tracking stock).
        $netMovement = 0.0;
        foreach ($events as $e) {
            $netMovement += match ($e['type']) {
                'purchase', 'adjustment' => $e['qty'],
                'return', 'sale' => -$e['qty'],
                default => 0.0,
            };
        }

        $currentQty = (float) ($variant->qty ?? 0);
        $legacy = $currentQty - $netMovement;

        if (abs($legacy) > 0.0001) {
            $seedCost = $this->toAmount($variant->average_cost ?? $variant->purchase_price);
            array_unshift($events, [
                'at' => '1970-01-01',
                'rank' => -1,
                'seq' => 0,
                'type' => 'opening',
                'qty' => $legacy,
                'cost' => $seedCost,
            ]);
        }

        $qty = 0.0;
        $value = 0.0;
        $openingQty = 0.0;
        $openingValue = 0.0;
        $purchaseQty = 0.0;
        $purchaseValue = 0.0;
        $salesQty = 0.0;
        $salesValue = 0.0;

        foreach ($events as $e) {
            $at = Carbon::parse($e['at']);
            $isOpeningEvent = $e['type'] === 'opening';
            $isBefore = $from ? $at->lt($from) : $isOpeningEvent;
            $isWithin = !$isOpeningEvent
                && ($from ? $at->gte($from) : true)
                && ($to ? $at->lte($to) : true);

            switch ($e['type']) {
                case 'opening':
                    $qty += $e['qty'];
                    $value += $e['qty'] * $e['cost'];
                    break;

                case 'purchase':
                    $qty += $e['qty'];
                    $value += $e['qty'] * $e['cost'];
                    if ($isWithin) {
                        $purchaseQty += $e['qty'];
                        $purchaseValue += $e['qty'] * $e['cost'];
                    }
                    break;

                case 'return':
                    $remove = min($e['qty'], $qty);
                    $value -= $remove * $e['cost'];
                    $qty -= $remove;
                    if ($isWithin) {
                        $purchaseQty -= $remove;
                        $purchaseValue -= $remove * $e['cost'];
                    }
                    break;

                case 'sale':
                    $unit = $qty > 0 ? ($value / $qty) : 0.0;
                    $remove = min($e['qty'], $qty);
                    $value -= $remove * $unit;
                    $qty -= $remove;
                    if ($isWithin) {
                        $salesQty += $remove;
                        $salesValue += $remove * $unit;
                    }
                    break;

                case 'adjustment':
                    // Adjustments change quantity but not the weighted-average cost,
                    // so they move stock at the running average. When there is no
                    // prior cost basis (e.g. adjusting into zero stock) fall back to
                    // the variant's current average/purchase cost so the value stays
                    // consistent with the application's inventory valuation.
                    $unit = $qty > 0
                        ? ($value / $qty)
                        : $this->toAmount($variant->average_cost ?? $variant->purchase_price);
                    if ($e['qty'] >= 0) {
                        $qty += $e['qty'];
                        $value += $e['qty'] * $unit;
                        if ($isWithin) {
                            $purchaseQty += $e['qty'];
                            $purchaseValue += $e['qty'] * $unit;
                        }
                    } else {
                        $remove = min(-$e['qty'], $qty);
                        $value -= $remove * $unit;
                        $qty -= $remove;
                        if ($isWithin) {
                            $salesQty += $remove;
                            $salesValue += $remove * $unit;
                        }
                    }
                    break;
            }

            if ($isBefore) {
                $openingQty = $qty;
                $openingValue = $value;
            }
        }

        return [
            'opening_qty' => $openingQty,
            'opening_value' => $openingValue,
            'opening_rate' => $openingQty != 0 ? $openingValue / $openingQty : 0.0,
            'purchase_qty' => $purchaseQty,
            'purchase_value' => $purchaseValue,
            'purchase_rate' => $purchaseQty != 0 ? $purchaseValue / $purchaseQty : 0.0,
            'sales_qty' => $salesQty,
            'sales_value' => $salesValue,
            'sales_rate' => $salesQty != 0 ? $salesValue / $salesQty : 0.0,
            'closing_qty' => $qty,
            'closing_value' => $value,
            'closing_rate' => $qty != 0 ? $value / $qty : 0.0,
        ];
    }

    protected function toAmount($value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        return round((float) $value, 2);
    }
}
