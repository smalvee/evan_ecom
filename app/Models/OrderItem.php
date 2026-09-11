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
}
