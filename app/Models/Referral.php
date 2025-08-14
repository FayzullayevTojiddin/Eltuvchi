<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'promo_code',
        'referred_by',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }
}