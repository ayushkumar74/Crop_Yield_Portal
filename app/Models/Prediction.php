<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'crop_id',
        'temperature',
        'rainfall',
        'humidity',
        'soil_ph',
        'predicted_yield',
        'suitability_score',
        'risk_level',
        'recommendation',
    ];

    /**
     * Get the user that owns the prediction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the crop that the prediction is for.
     */
    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class);
    }
}
