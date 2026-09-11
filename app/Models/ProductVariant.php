<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;
    protected $fillable = ['product_id', 'sku', 'is_variant', 'variation_sku', 'variation_values', 'purchase_price', 'selling_price', 'compare_price', 'qty'];

    public function product()
    {
        return $this->belongsTo(NewProduct::class, 'product_id');
    }

    // protected $casts = [
    //     'variation_values' => 'array',
    // ];
}
