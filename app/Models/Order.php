<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public static function isFreeDeliveryCart($items): bool
    {
        if ($items->isEmpty()) {
            return false;
        }

        return $items->every(fn ($item) => (int) ($item->options->get('freeDelivery') ?? 0) === 1);
    }
}
