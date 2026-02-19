<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    protected $fillable = [
        'device_id',
        'species',
        'location',
    ];

    public function readings(): HasMany
    {
        return $this->hasMany(WaterReading::class, 'device_id', 'device_id');
    }
}
