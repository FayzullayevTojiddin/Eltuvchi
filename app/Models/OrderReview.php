<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $order_id
 * @property int $client_id
 * @property int $score
 * @property string|null $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Client $client
 * @property-read \App\Models\Order $order
 * @method static \Database\Factories\OrderReviewFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderReview newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderReview newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderReview query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderReview whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderReview whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderReview whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderReview whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderReview whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderReview whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderReview whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OrderReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'client_id',
        'score',
        'comment',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}