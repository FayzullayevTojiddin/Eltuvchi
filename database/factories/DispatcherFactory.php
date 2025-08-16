<?php

namespace Database\Factories;

use App\Models\Dispatcher;
use App\Models\TaxoPark;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DispatcherFactory extends Factory
{
    protected $model = Dispatcher::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'taxopark_id' => TaxoPark::factory(),
            'full_name' => $this->faker->name(),
            'status' => 'active',
            'details' => [
                'phone' => $this->faker->phoneNumber(),
            ],
        ];
    }
}