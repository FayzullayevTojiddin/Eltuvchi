<?php

namespace App\Traits;

use App\Models\PointHistory;

trait HasPoint
{
    public function addPoints(int $points, string $description = null): void
    {
        $this->increment('points', $points);

        PointHistory::create([
            'pointable_id' => $this->id,
            'pointable_type' => static::class,
            'points' => $points,
            'type' => 'plus',
            'points_after' => $this->points,
            'description' => $description,
        ]);
    }

    public function subtractPoints(int $points, string $description = null): bool
    {
        if ($this->points < $points) {
            return false;
        }

        $this->decrement('points', $points);

        PointHistory::create([
            'pointable_id' => $this->id,
            'pointable_type' => static::class,
            'points' => $points,
            'type' => 'minus',
            'points_after' => $this->points,
            'description' => $description,
        ]);

        return true;
    }

    public function pointHistories()
    {
        return $this->morphMany(PointHistory::class, 'pointable');
    }
}