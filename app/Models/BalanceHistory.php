<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $balanceable_type
 * @property int $balanceable_id
 * @property int $amount
 * @property string $type
 * @property int $balance_after
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $balanceable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceHistory whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceHistory whereBalanceAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceHistory whereBalanceableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceHistory whereBalanceableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceHistory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceHistory whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceHistory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BalanceHistory extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'balanceable_id',
        'balanceable_type',
        'amount',
        'type',
        'balance_after',
        'description',
    ];

    public function balanceable()
    {
        return $this->morphTo();
    }
}