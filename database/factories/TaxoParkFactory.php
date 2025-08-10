<?php

namespace Database\Factories;

use App\Models\Region;
use App\Models\TaxoPark;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaxoParkFactory extends Factory
{
    protected $model = TaxoPark::class;

    public function definition(): array
    {
        return [
            'region_id' => Region::factory(), // To‘g‘ri bog‘lanish
            'name' => $this->faker->company . ' Taxi Park',
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}