<?php

namespace Database\Factories;

use App\Models\Discount;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiscountFactory extends Factory
{
    protected $model = Discount::class;

    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(['percent', 'fixed']),
            'value' => $this->faker->numberBetween(5, 50),
            'points' => $this->faker->numberBetween(0, 100),
            'title' => $this->faker->words(2, true),
            'icon' => $this->faker->randomElement(['percent', 'gift', 'cash', 'star']),
        ];
    }
}