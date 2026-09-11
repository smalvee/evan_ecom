<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variation extends Model
{
    use HasFactory;
    protected $fillable = ['variations'];

     public function values()
    {
        return $this->hasMany(VariationValues::class, 'variation_id');
    }
}
