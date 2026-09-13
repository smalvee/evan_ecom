<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

/**
 * Central pre-order rules.
 *
 * Three customer-facing states (see the pre-order spec):
 *   1. stock > 0                         -> normal purchase.
 *   2. stock <= 0, allow_pre_order false -> purchase rejected ("Stock Out").
 *   3. stock <= 0, allow_pre_order true  -> pre-order allowed (no stock deducted).
 *
 * Eligibility is always decided server-side from the variant's own columns; the
 * browser's is_pre_order / stock / allow_pre_order / price are never trusted.
 */
class PreOrderService
{
    /**
     * Evaluate whether $qty of $variant can be purchased, and whether that
     * purchase is a pre-order.
     *
     * @return array{ok: bool, is_pre_order: bool, available: int|null, message: string|null}
     */
    public function evaluate(ProductVariant $variant, int $qty): array
    {
        $qty = max(1, $qty);
        $stock = $variant->stock();

        // Untracked stock behaves as unlimited (existing behaviour).
        if ($stock === null) {
            return ['ok' => true, 'is_pre_order' => false, 'available' => null, 'message' => null];
        }

        if ($stock >= $qty) {
            return ['ok' => true, 'is_pre_order' => false, 'available' => $stock, 'message' => null];
        }

        // Some stock exists but not enough: reject. Pre-order is not offered while
        // the item is (partially) in stock.
        if ($stock > 0) {
            return [
                'ok' => false,
                'is_pre_order' => false,
                'available' => $stock,
                'message' => 'Only ' . $stock . ' items in stock',
            ];
        }

        // Out of stock.
        if ($variant->allow_pre_order) {
            return ['ok' => true, 'is_pre_order' => true, 'available' => 0, 'message' => null];
        }

        return [
            'ok' => false,
            'is_pre_order' => false,
            'available' => 0,
            'message' => 'This item is out of stock.',
        ];
    }

    /**
     * True when the variant tracks enough stock to cover $qty (untracked = true).
     */
    public function hasSufficientStock(ProductVariant $variant, int $qty): bool
    {
        $stock = $variant->stock();

        return $stock === null || $stock >= $qty;
    }

    /**
     * Admin action: process a pre-order (or any order containing pre-order items).
     *
     * Runs in a single transaction, locks the order row so two admins cannot
     * process it simultaneously, re-checks current stock, then deducts stock
     * exactly once and moves the order into the standard "confirm" workflow.
     *
     * @return array{status: bool, message: string, deducted: bool}
     */
    public function processOrder(Order $order): array
    {
        return DB::transaction(function () use ($order) {
            $locked = Order::with('items')->lockForUpdate()->find($order->id);

            if (!$locked) {
                return ['status' => false, 'message' => 'Order not found.', 'deducted' => false];
            }

            if ($locked->status === 'cancell') {
                return ['status' => false, 'message' => 'Cancelled orders cannot be processed.', 'deducted' => false];
            }

            $preOrderItems = $locked->items->where('is_pre_order', true);

            if ($preOrderItems->isEmpty()) {
                return ['status' => false, 'message' => 'This order has no pre-order items.', 'deducted' => false];
            }

            // Already deducted through the normal workflow: only sync the status.
            if ($locked->stock_deducted) {
                $this->markPreOrderStatus($locked, 'processing');

                return ['status' => true, 'message' => 'Pre-order already processed.', 'deducted' => false];
            }

            // Re-check stock for every item (normal + pre-order) before changing anything.
            foreach ($locked->items as $item) {
                $variant = ProductVariant::where('id', $item->product_id)->lockForUpdate()->first();

                if (!$variant) {
                    return ['status' => false, 'message' => 'A product variant no longer exists.', 'deducted' => false];
                }

                if (!$this->hasSufficientStock($variant, (int) $item->qty)) {
                    $available = $variant->stock() ?? 'unlimited';

                    return [
                        'status' => false,
                        'message' => 'Insufficient stock for ' . ($variant->sku ?? ('variant #' . $variant->id))
                            . '. Required: ' . (int) $item->qty . ', available: ' . $available . '.',
                        'deducted' => false,
                    ];
                }
            }

            // Deduct stock exactly once for every item.
            foreach ($locked->items as $item) {
                $variant = ProductVariant::where('id', $item->product_id)->lockForUpdate()->first();
                $variant->decrement('qty', (int) $item->qty);
            }

            $locked->stock_deducted = true;
            $locked->status = 'confirm';
            $locked->save();

            $this->markPreOrderStatus($locked, 'processing');

            return ['status' => true, 'message' => 'Pre-order processed successfully.', 'deducted' => true];
        });
    }

    /**
     * Update the pre-order workflow state of an order's pre-order items.
     * "processing" never downgrades an item that already moved past pending.
     */
    public function markPreOrderStatus(Order $order, string $status): void
    {
        $query = OrderItem::where('order_id', $order->id)->where('is_pre_order', true);

        if ($status === 'processing') {
            $query->where(function ($q) {
                $q->whereNull('pre_order_status')->orWhere('pre_order_status', 'pending');
            });
        }

        $query->update(['pre_order_status' => $status]);
    }
}
