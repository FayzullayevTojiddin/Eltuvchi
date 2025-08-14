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
            'points' => $this->faker->numberBetween(1, 100),
            'type' => $this->faker->randomElement(['plus', 'minus']),
            'points_after' => $this->faker->numberBetween(1, 100),
            'description' => $this->faker->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}