<?php

namespace App\Traits;

use App\Models\BalanceHistory;

trait HasBalance
{
    public function addBalance(int $amount, string $description = null): void
    {
        $this->increment('balance', $amount);
        $this->refresh();

        BalanceHistory::create([
            'balanceable_id' => $this->id,
            'balanceable_type' => static::class,
            'amount' => $amount,
            'type' => 'plus',
            'balance_after' => $this->balance,
            'description' => $description,
        ]);
    }

    public function subtractBalance(int $amount, string $description = null): bool
    {
        if ($this->balance < $amount) {
            return false;
        }

        $this->decrement('balance', $amount);
        $this->refresh();

        BalanceHistory::create([
            'balanceable_id' => $this->id,
            'balanceable_type' => static::class,
            'amount' => $amount,
            'type' => 'minus',
            'balance_after' => $this->balance,
            'description' => $description,
        ]);

        return true;
    }

    public function balanceHistories()
    {
        return $this->morphMany(BalanceHistory::class, 'balanceable');
    }
}