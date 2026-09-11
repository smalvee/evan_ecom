<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'product_id', 'name', 'qty', 'price', 'discount', 'total', 'free_delivery'];

    protected $casts = [
        'free_delivery' => 'boolean',
    ];

    /**
     * The product variant this order item refers to (order_items.product_id => product_variants.id).
     */
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_id');
    }
}
