<?php

namespace App\Models;

use App\Traits\HasBalance;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $role
 * @property string|null $email
 * @property string|null $password
 * @property string|null $telegram_id
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Client|null $client
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTelegramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
        return $this->connected()?->full_name ?? $this->email ?? 'No name';
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