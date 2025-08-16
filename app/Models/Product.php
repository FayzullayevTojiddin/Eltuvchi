<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'icon_type',
        'points',
        'title',
        'description',
    ];

    public function driverProducts()
    {
        return $this->hasMany(DriverProduct::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}