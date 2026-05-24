<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
<<<<<<< HEAD
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
=======
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'avatar',
        'role',
        'password',
        'latitude',
        'longitude',
        'last_detected_location',
        'location_permission_granted',
        'theme_preference',
        'language_preference',
        'notification_preferences',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'latitude' => 'double',
            'longitude' => 'double',
            'location_permission_granted' => 'boolean',
            'notification_preferences' => 'array',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

<<<<<<< HEAD
    public function avatarUrl(): ?string
    {
        if ($this->avatar === null || $this->avatar === '') {
            return null;
        }

        if (Str::startsWith($this->avatar, ['http://', 'https://', '//'])) {
            return $this->avatar;
        }

        return Storage::disk('public')->url($this->avatar);
    }

=======
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class);
    }

<<<<<<< HEAD
    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }

=======
>>>>>>> ad0ccee2af44b30e9d0ff7fdf2eb6cb6db219755
    public function latestPrediction()
    {
        return $this->hasOne(Prediction::class)->latestOfMany();
    }
}
