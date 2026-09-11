<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'status_01' => 'boolean',
        'status_02' => 'boolean',
        'status_03' => 'boolean',
        'status_04' => 'boolean',
        'status_05' => 'boolean',
        'status_06' => 'boolean',
        'status_07' => 'boolean',
    ];

    /**
     * Whether the given advertisement slot (1-7) is active.
     * Defaults to active when the status column is missing or null.
     */
    public function isSlotActive(int $slot): bool
    {
        $key = 'status_0' . $slot;

        return (bool) ($this->{$key} ?? true);
    }
}
