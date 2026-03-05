<?php

namespace App\Models;

use App\Jobs\AnalyzeWaterQuality;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaterReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'turbidity',
        'tds',
        'ph',
        'temperature',
        'no_water_detected',
    ];

    protected $casts = [
        'turbidity' => 'decimal:2',
        'tds' => 'decimal:2',
        'ph' => 'decimal:2',
        'temperature' => 'decimal:2',
        'no_water_detected' => 'boolean',
    ];

    public function waterAnalyses(): HasMany
    {
        return $this->hasMany(WaterAnalysis::class);
    }

    public function latestAnalysis(): HasMany
    {
        return $this->waterAnalyses()->latest();
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_id', 'device_id');
    }

    // Analysis is triggered by the scheduled command (every 5 minutes).

    public function getPhStatusAttribute(): string
    {
        $val = $this->ph;
        if ($val < 6.0 || $val > 9.0) return 'Critical';
        if ($val < 6.5 || $val > 8.5) return 'Warning';
        return 'Normal';
    }

    public function getTdsStatusAttribute(): string
    {
        $val = $this->tds;
        if ($val > 1000) return 'Critical';
        if ($val > 500 || $val < 300) return 'Warning';
        return 'Normal';
    }

    public function getTurbidityStatusAttribute(): string
    {
        $val = $this->turbidity;
        if ($val < 20) return 'Critical';
        if ($val < 50) return 'Warning';
        return 'Normal';
    }

    public function getTemperatureStatusAttribute(): string
    {
        $val = $this->temperature;
        if ($val < 15 || $val > 35) return 'Critical';
        if ($val < 25 || $val > 32) return 'Warning';
        return 'Normal';
    }
}
