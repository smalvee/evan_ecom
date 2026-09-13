<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'product_id', 'name', 'qty', 'price', 'cost_price', 'discount', 'total', 'free_delivery', 'is_pre_order', 'pre_order_status'];

    protected $casts = [
        'free_delivery' => 'boolean',
        'is_pre_order' => 'boolean',
        'price' => 'float',
        'cost_price' => 'float',
        'total' => 'float',
    ];

    /**
     * Human label for the pre-order workflow state.
     */
    public function preOrderStatusLabel(): ?string
    {
        if (!$this->is_pre_order) {
            return null;
        }

        return match ($this->pre_order_status) {
            'processing' => 'Processing',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => 'Pending',
        };
    }

    /**
     * The product variant this order item refers to (order_items.product_id => product_variants.id).
     */
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_id');
    }

    /**
     * Preferred image for the ordered variant. In the new product system
     * `product_images.product_id` holds the variant id, so it lines up with
     * `order_items.product_id`. Thumbnails are preferred, then gallery order.
     */
    public function image()
    {
        return $this->hasOne(ProductImage::class, 'product_id', 'product_id')
            ->orderByDesc('is_thumb')
            ->orderBy('sort_order');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
