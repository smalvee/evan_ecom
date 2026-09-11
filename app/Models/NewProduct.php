<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewProduct extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug', 'sku', 'unit_id', 'brand_id', 'cat_id', 'sub_cat_id', 'description', 'type', 'status', 'hot_products'];

    public function product_variation()
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }
    
}
