<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'sku', 'is_variant', 'variation_sku', 'variation_values', 'purchase_price', 'average_cost', 'selling_price', 'compare_price', 'qty', 'price_manually_managed'];

    protected $casts = [
        'price_manually_managed' => 'boolean',
        'average_cost' => 'float',
        'purchase_price' => 'float',
        'selling_price' => 'float',
        'compare_price' => 'float',
        'qty' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(NewProduct::class, 'product_id');
    }

    /**
     * Manual MRP / selling-price change history for this variant.
     */
    public function priceHistories()
    {
        return $this->hasMany(ProductVariantPriceHistory::class, 'product_variant_id');
    }
}
