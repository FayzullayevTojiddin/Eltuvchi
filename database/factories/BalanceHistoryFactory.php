<?php

namespace Database\Factories;

use App\Models\BalanceHistory;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class BalanceHistoryFactory extends Factory
{
    protected $model = BalanceHistory::class;

    public function definition()
    {
        return [
            'amount' => $this->faker->numberBetween(1000, 100000),
            'type' => $this->faker->randomElement(['plus', 'minus']),
            'balance_after' => $this->faker->numberBetween(10000, 200000),
            'description' => $this->faker->optional()->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}