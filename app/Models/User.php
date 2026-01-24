<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasApiTokens;

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'superAdmin' => $this->role === 'superadmin',
            'taxoParkAdmin' => $this->role === 'taxoparkadmin',
            default => false,
        };
    }

    public function getNameAttribute(): string
    {
        if ($this->role === 'client') {
            $settings = is_array($this->client?->settings) 
                ? $this->client->settings 
                : json_decode($this->client?->settings ?? '{}', true);
            
            return $settings['full_name'] ?? $this->email ?? 'No name';
        }
        
        if ($this->role === 'driver') {
            $details = is_array($this->driver?->details) 
                ? $this->driver->details 
                : json_decode($this->driver?->details ?? '{}', true);
                
            return $details['full_name'] ?? $this->email ?? 'No name';
        }
        
        return $this->email ?? 'No name';
    }
    
    protected $fillable = [
        'role',
        'email',
        'password',
        'telegram_id',
        'telegram_state'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function client(): HasOne
    {
        return $this->hasOne(Client::class);
    }

    public function driver(): HasOne
    {
        return $this->hasOne(Driver::class);
    }

    public function superAdmin(): HasOne
    {
        return $this->hasOne(SuperAdmin::class);
    }

    public function taxoparkadmin(): HasOne
    {
        return $this->hasOne(Dispatcher::class);
    }

    public function referral()
    {
        return $this->hasOne(Referral::class);
    }

    public function referralsMade()
    {
        return $this->hasMany(Referral::class, 'referred_by');
    }

    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->promo_code)) {
                $user->promo_code = strtoupper(substr(md5(uniqid()), 0, 8));
            }
        });
    }

    public function connected()
    {
        return match($this->role) {
            'client' => $this->client,
            'driver' => $this->driver,
            'superadmin' => $this->superAdmin,
            'taxoparkadmin' => $this->taxoParkAdmin,
            default => null,
        };
    }

    public function getWhereAttribute()
    {
        return match ($this->role) {
            'client' => $this->client,
            'driver' => $this->driver,
            'superadmin' => $this->superAdmin,
            'taxoparkadmin' => $this->taxoParkAdmin,
            default => null,
        };
    }

    public function dispatcher(): HasOne
    {
        return $this->hasOne(Dispatcher::class);
    }

    public function getDisplayNameAttribute(): ?string
    {
        return match($this->role) {
            'superadmin' => $this->superAdmin?->full_name,
            'taxoparkadmin' => $this->taxoParkAdmin?->full_name,
            'driver' => $this->driver?->details['full_name'] ?? null,
            'client' => $this->client?->settings['full_name'] ?? null,
            default => $this->name,
        };
    }
}