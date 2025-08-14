<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointHistory extends Model
{
    protected $fillable = [
        'pointable_id',
        'pointable_type',
        'points',
        'type',
        'points_after',
        'description',
    ];
}