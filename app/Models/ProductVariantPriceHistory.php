<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariantPriceHistory extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'old_compare_price' => 'float',
        'new_compare_price' => 'float',
        'old_selling_price' => 'float',
        'new_selling_price' => 'float',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
