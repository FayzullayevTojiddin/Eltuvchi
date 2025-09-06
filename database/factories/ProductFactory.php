<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'status'      => 'active',
            'icon_type'   => $this->faker->randomElement(['car', 'bonus', 'gift', 'star']),
            'points'      => $this->faker->numberBetween(5, 100),
            'title'       => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
        ];
    }
}