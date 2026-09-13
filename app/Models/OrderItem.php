<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'product_id', 'name', 'qty', 'price', 'cost_price', 'discount', 'total', 'free_delivery'];

    protected $casts = [
        'free_delivery' => 'boolean',
        'price' => 'float',
        'cost_price' => 'float',
        'total' => 'float',
    ];

    /**
     * The product variant this order item refers to (order_items.product_id => product_variants.id).
     */
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_id');
    }
}
