<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'contact',
        'address',
        'address_region_code',
        'address_province_code',
        'address_city_code',
        'address_barangay_code',
        'address_street',
        'address_postal_code',
        'role',
        'profile_photo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'banned_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getIsBannedAttribute(): bool
    {
        return !is_null($this->banned_at);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getNameAttribute(): string
    {
        return trim(((string) ($this->first_name ?? '')) . ' ' . ((string) ($this->last_name ?? '')));
    }

    public function setNameAttribute($value): void
    {
        $name = trim((string) $value);
        if ($name === '') {
            $this->attributes['first_name'] = '';
            $this->attributes['last_name'] = '';
            return;
        }

        $parts = preg_split('/\s+/', $name) ?: [];
        $first = $parts[0] ?? $name;
        $last = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';

        $this->attributes['first_name'] = $first;
        $this->attributes['last_name'] = $last;
    }

    
}
