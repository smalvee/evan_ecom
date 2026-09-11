<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariationValues extends Model
{
    use HasFactory;
     protected $fillable = [
        'variation_id',
        'value',
    ];

      public function variation()
    {
        return $this->belongsTo(Variation::class, 'variation_id');
    }
}
