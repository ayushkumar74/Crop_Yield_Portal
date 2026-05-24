<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Crop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'min_temp',
        'max_temp',
        'min_rainfall',
        'max_rainfall',
        'min_humidity',
        'max_humidity',
        'base_yield',
    ];

    /**
     * Get the translated crop name.
     */
    public function getTranslatedCropNameAttribute(): string
    {
        $key = 'messages.crop_'.strtolower(str_replace(' ', '_', $this->name));

        return __($key) !== $key ? __($key) : $this->name;
    }

    /**
     * Get the translated name.
     */
    public function getTranslatedNameAttribute(): string
    {
        return $this->translated_crop_name;
    }

    /**
     * Get the predictions for the crop.
     */
    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class);
    }
}
