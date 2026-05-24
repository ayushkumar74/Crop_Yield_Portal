<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginOtp extends Model
{
    protected $fillable = ['email', 'otp', 'expires_at', 'used'];

    protected $casts = [
        'expires_at' => 'datetime',
        'used' => 'boolean',
    ];

    /**
     * Check if this OTP is still valid (not expired, not used).
     */
    public function isValid(): bool
    {
        return ! $this->used && $this->expires_at->isFuture();
    }
}
