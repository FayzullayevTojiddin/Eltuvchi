<?php

namespace App\Models;

use App\Traits\HasBalance;
use App\Traits\HasPoint;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'taxopark_id',
        'full_name'
    ];

    protected $casts = [
        'details' => AsArrayObject::class,
        'settings' => AsArrayObject::class,
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

    protected static function booted()
    {
        static::created(function (Driver $driver) {
            $driver->user->update([
                'role' => 'driver',
            ]);
        });

        static::deleting(function ($model) {
            if ($model->user) {
                $model->user->delete();
            }
        });
    }
}