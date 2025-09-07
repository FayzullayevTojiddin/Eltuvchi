<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalanceHistory extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'balanceable_id',
        'balanceable_type',
        'amount',
        'type',
        'balance_after',
        'description',
        'user_id',
    ];

    public function balanceable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}