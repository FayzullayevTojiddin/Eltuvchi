<?php

namespace App\Models;

use App\Traits\HasBalance;
use App\Traits\HasPoint;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property string $status
 * @property int $balance
 * @property int $points
 * @property array<array-key, mixed>|null $settings
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BalanceHistory> $balanceHistories
 * @property-read int|null $balance_histories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PointHistory> $pointHistories
 * @property-read int|null $point_histories_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\ClientFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client whereUserId($value)
 * @mixin \Eloquent
 */
class Client extends Model
{
    use HasFactory, HasPoint, HasBalance;

    protected $fillable = [
        'user_id',
        'status',
        'balance',
        'points',
        'settings',
    ];

    protected $casts = [
        'settings' => AsArrayObject::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function canPayDeposit(int $amount): bool
    {
        return $this->balance >= $amount;
    }

    public function payDeposit(int $amount): bool
    {
        return $this->subtractBalance($amount, 'Order deposit payment');
    }

    public function discounts(): HasMany
    {
        return $this->hasMany(ClientDiscount::class);
    }

    protected static function booted()
    {
        static::created(function (Client $client) {
            $client->user->update([
                'role' => 'client',
            ]);
        });

        static::deleting(function ($model) {
            if ($model->user) {
                $model->user->delete();
            }
        });
    }
}