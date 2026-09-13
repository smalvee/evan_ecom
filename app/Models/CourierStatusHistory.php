<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'courier_shipment_id',
        'status',
        'message',
        'raw_response',
    ];

    protected $casts = [
        'raw_response' => 'array',
    ];

    public function shipment()
    {
        return $this->belongsTo(CourierShipment::class, 'courier_shipment_id');
    }
}
