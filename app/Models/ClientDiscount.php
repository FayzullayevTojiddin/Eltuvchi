<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $client_id
 * @property int $discount_id
 * @property int $used
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Client $client
 * @property-read \App\Models\Discount $discount
 * @method static \Database\Factories\ClientDiscountFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientDiscount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientDiscount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientDiscount query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientDiscount whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientDiscount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientDiscount whereDiscountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientDiscount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientDiscount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientDiscount whereUsed($value)
 * @mixin \Eloquent
 */
class ClientDiscount extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'client_id',
        'discount_id',
        'used',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    public static function getUnusedByClientAndDiscount(int $clientId, ?int $discountId): ?self
    {
        if (!$discountId) {
            return null;
        }
        return self::where('client_id', $clientId)
            ->where('discount_id', $discountId)
            ->where('used', false)
            ->with('discount')
            ->first();
    }

    public function markUsed(): void
    {
        $this->update(['used' => true]);
    }
}