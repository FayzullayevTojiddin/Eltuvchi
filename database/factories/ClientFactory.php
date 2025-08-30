<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // Yangi user yaratiladi
            'status' => 'active',         // Status default holatda
            'balance' => $this->faker->numberBetween(0, 100000), // Tasodifiy balans
            'points' => $this->faker->numberBetween(0, 1000),    // Ballar
            'settings' => [
                'notifications' => true,
                'language' => 'en',
                'full_name' => $this->faker->firstName() + " " + $this->faker->lastName()
            ],
        ];
    }
}