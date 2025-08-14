<?php

namespace Database\Factories;

use App\Models\Driver;
use App\Models\TaxoPark;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DriverFactory extends Factory
{
    protected $model = Driver::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'taxopark_id' => TaxoPark::factory(),
            'status' => 'active',
            'balance' => $this->faker->numberBetween(0, 100000),
            'points' => $this->faker->numberBetween(0, 1000),
            'details' => [
                'vehicle' => $this->faker->word(),
                'experience_years' => $this->faker->numberBetween(1, 10),
            ],
            'settings' => [
                'notifications' => true,
                'language' => 'uz',
            ],
        ];
    }
}