<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $client_id
 * @property int|null $driver_id
 * @property int $route_id
 * @property int $passengers
 * @property \Illuminate\Support\Carbon $date
 * @property string $time
 * @property numeric $price_order
 * @property numeric $client_deposit
 * @property numeric|null $driver_payment
 * @property int|null $discount_percent
 * @property numeric|null $discount_summ
 * @property string $phone
 * @property string|null $optional_phone
 * @property string|null $note
 * @property OrderStatus $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Client $client
 * @property-read \App\Models\Driver|null $driver
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderHistory> $histories
 * @property-read int|null $histories_count
 * @property-read \App\Models\Route $route
 * @method static \Database\Factories\OrderFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereClientDeposit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDiscountPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDiscountSumm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDriverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDriverPayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereOptionalPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order wherePassengers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order wherePriceOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Order extends Model
{
    use HasFactory;

    public $temp_description;

    protected $fillable = [
        'client_id',
        'driver_id',
        'route_id',
        'passengers',
        'date',
        'time',
        'price_order',
        'client_deposit',
        'driver_payment',
        'discount_percent',
        'discount_summ',
        'phone',
        'optional_phone',
        'note',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i',
        'price_order' => 'decimal:2',
        'client_deposit' => 'decimal:2',
        'driver_payment' => 'decimal:2',
        'discount_percent' => 'integer',
        'discount_summ' => 'decimal:2',
        'status' => OrderStatus::class,
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function logStatusChange(string $status, $user, ?string $description = null): void
    {
        $this->histories()->create([
            'status' => $status,
            'changed_by_id' => $user->id,
            'changed_by_type' => get_class($user),
            'description' => $description,
        ]);
    }

    public function histories()
    {
        return $this->hasMany(OrderHistory::class);
    }

    public static function createNewOrder(int $clientId, array $data, float $priceOrder, int $discountPercent, float $discountSumm): self
    {
        return self::create([
            'client_id' => $clientId,
            'driver_id' => null,
            'route_id' => $data['route_id'],
            'passengers' => $data['passengers'],
            'date' => $data['date'],
            'time' => $data['time'],
            'price_order' => $priceOrder,
            'client_deposit' => $data['client_deposit'],
            'driver_payment' => null,
            'discount_percent' => $discountPercent,
            'discount_summ' => $discountSumm,
            'phone' => $data['phone'],
            'optional_phone' => $data['optional_phone'] ?? null,
            'note' => $data['note'] ?? null,
            'status' => OrderStatus::Created->value,
        ]);
    }

    public function review(): HasOne
    {
        return $this->hasOne(OrderReview::class);
    }
}