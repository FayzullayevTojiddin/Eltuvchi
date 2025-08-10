<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\OrderStatus;

/**
 * @property int $id
 * @property int $order_id
 * @property OrderStatus $status
 * @property int|null $changed_by_id
 * @property string|null $changed_by_type
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order $order
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory whereChangedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory whereChangedByType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OrderHistory extends Model
{
    protected $fillable = [
        'order_id',
        'status',
        'changed_by_id',
        'changed_by_type',
        'description',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}