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
            'type' => 'percent',
            'value' => $this->faker->numberBetween(10, 100),
            'points' => $this->faker->numberBetween(10, 100),
            'title' => $this->faker->sentence(3),
            'icon' => null,
            'status' => Discount::STATUS_ACTIVE,
        ];
    }

    public function inactive()
    {
        return $this->state(fn () => [
            'status' => 'inactive',
        ]);
    }
}