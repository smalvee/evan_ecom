<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingCharge extends Model
{
    use HasFactory;

    protected $fillable = ['location', 'district', 'amount'];

    public static function ratesByDistrict(): array
    {
        return static::whereNotNull('district')
            ->pluck('amount', 'district')
            ->map(fn ($amount) => (float) $amount)
            ->toArray();
    }

    public static function rateForDistrict($district): float
    {
        return (float) (static::ratesByDistrict()[$district] ?? 0);
    }
}
