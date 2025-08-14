<?php

namespace Database\Factories;

use App\Models\PointHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

class PointHistoryFactory extends Factory
{
    protected $model = PointHistory::class;

    public function definition()
    {
        return [
            'pointable_id' => 1,
            'pointable_type' => 'App\\Models\\User',
            'points' => $this->faker->numberBetween(1, 100),
            'type' => $this->faker->randomElement(['plus', 'minus']),
            'points_after' => $this->faker->numberBetween(1, 100),
            'description' => $this->faker->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}