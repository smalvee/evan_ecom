<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutUs extends Model
{
    use HasFactory;

    protected $table = 'about_us'; // in case you renamed the table

    protected $fillable = ['who_we_are', 'our_mission', 'our_vision', 'refund_policy', 'return_policy'];
}
