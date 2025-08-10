<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $user_id
 * @property int $taxopark_id
 * @property string $full_name
 * @property string $status
 * @property array<array-key, mixed>|null $details
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\TaxoPark $taxopark
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispatcher newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispatcher newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispatcher query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispatcher whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispatcher whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispatcher whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispatcher whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispatcher whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispatcher whereTaxoparkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispatcher whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Dispatcher whereUserId($value)
 * @mixin \Eloquent
 */
class Dispatcher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'taxopark_id',
        'full_name',
        'status',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function taxopark()
    {
        return $this->belongsTo(TaxoPark::class);
    }
}