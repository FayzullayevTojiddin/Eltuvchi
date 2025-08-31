<?php

namespace App\Models;

use App\Traits\HasBalance;
use App\Traits\HasPoint;
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
 * @property array<array-key, mixed>|null $details
 * @property array<array-key, mixed>|null $settings
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BalanceHistory> $balanceHistories
 * @property-read int|null $balance_histories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PointHistory> $pointHistories
 * @property-read int|null $point_histories_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\DriverFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Driver newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Driver newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Driver query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Driver whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Driver whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Driver whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Driver whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Driver wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Driver whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Driver whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Driver whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Driver whereUserId($value)
 * @mixin \Eloquent
 */
class Driver extends Model
{
    use HasFactory, HasBalance, HasPoint;

    protected $fillable = [
        'user_id',
        'status',
        'balance',
        'points',
        'details',
        'settings',
        'taxopark_id'
    ];

    protected $casts = [
        'details' => 'array',
        'settings' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function taxopark(): BelongsTo
    {
        return $this->belongsTo(TaxoPark::class, 'taxopark_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(DriverProduct::class);
    }
}