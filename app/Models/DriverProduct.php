<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DriverProduct extends Model
{
    protected $fillable = [
        'driver_id',
        'product_id',
        'delivered',
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function isDelivered(): bool
    {
        return (bool) $this->delivered;
    }
}