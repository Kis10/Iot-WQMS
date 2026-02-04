<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaterAnalysis extends Model
{
    protected $fillable = [
        'water_reading_id',
        'analysis_type',
        'ai_insight',
        'risk_level',
        'recommendations',
        'confidence_score',
        'analyzed_at',
    ];

    protected $casts = [
        'recommendations' => 'array',
        'analyzed_at' => 'datetime',
        'confidence_score' => 'decimal:2',
    ];

    public function waterReading(): BelongsTo
    {
        return $this->belongsTo(WaterReading::class);
    }
}
