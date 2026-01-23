<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepositTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'click_trans_id',
        'merchant_trans_id',
        'status',
        'error_note',
        'completed_at'
    ];

    protected $casts = [
        'completed_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsSuccess($clickTransId)
    {
        $this->update([
            'status' => 'success',
            'click_trans_id' => $clickTransId,
            'completed_at' => now()
        ]);
    }

    public function markAsFailed($errorNote = null)
    {
        $this->update([
            'status' => 'failed',
            'error_note' => $errorNote
        ]);
    }
}