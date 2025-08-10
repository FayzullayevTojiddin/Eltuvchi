<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $region_id
 * @property string $name
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Region $region
 * @method static \Database\Factories\TaxoParkFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxoPark newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxoPark newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxoPark query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxoPark whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxoPark whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxoPark whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxoPark whereRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxoPark whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxoPark whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TaxoPark extends Model
{
    use HasFactory;

    protected $fillable = [
        'region_id',
        'name',
        'status',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}