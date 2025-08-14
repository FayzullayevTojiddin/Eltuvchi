<?php
namespace App\Models;
use App\Enums\RouteStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $taxopark_from_id
 * @property int $taxopark_to_id
 * @property RouteStatus $status
 * @property int $deposit_client
 * @property int $distance_km
 * @property int $price_in
 * @property int $fee_per_client
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\TaxoPark $fromTaxopark
 * @property-read \App\Models\TaxoPark $toTaxopark
 * @method static \Database\Factories\RouteFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route whereDepositClient($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route whereDistanceKm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route whereFeePerClient($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route wherePriceIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route whereTaxoparkFromId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route whereTaxoparkToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Route whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'taxopark_from_id',
        'taxopark_to_id',
        'status',
        'deposit_client',
        'distance_km',
        'price_in',
        'fee_per_client',
    ];

    protected $casts = [
        'status' => RouteStatus::class,
    ];

    public function fromTaxopark(): BelongsTo
    {
        return $this->belongsTo(TaxoPark::class, 'taxopark_from_id');
    }

    public function toTaxopark(): BelongsTo
    {
        return $this->belongsTo(TaxoPark::class, 'taxopark_to_id');
    }

    public function calculatePrice(int $passengers): float
    {
        return $this->price_in * $passengers;
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}