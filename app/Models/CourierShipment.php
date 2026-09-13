<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Local record of an order's courier consignment.
 *
 * `active` = 1 marks the order's single in-flight shipment; cancelled/failed
 * shipments release the slot (NULL) so a retry is possible. The unique index
 * (order_id, active) enforces this at the database level.
 */
class CourierShipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'provider',
        'mode',
        'invoice',
        'consignment_id',
        'tracking_code',
        'status',
        'cod_amount',
        'delivery_type',
        'recipient_name',
        'recipient_phone',
        'recipient_address',
        'item_description',
        'total_lot',
        'response_data',
        'active',
    ];

    protected $casts = [
        'cod_amount' => 'float',
        'total_lot' => 'integer',
        'response_data' => 'array',
        // NOTE: `active` is intentionally NOT cast to boolean. It must stay
        // NULL when released (cancelled/failed) so MySQL's unique index
        // (order_id, active) allows a retry without a 0-value collision.
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function statusHistories()
    {
        return $this->hasMany(CourierStatusHistory::class, 'courier_shipment_id')->latest('id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function isActive(): bool
    {
        return (int) $this->active === 1;
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isDelivered(): bool
    {
        return in_array($this->status, ['delivered', 'partial_delivered'], true);
    }

    public function isTest(): bool
    {
        return $this->mode !== 'live';
    }
}
