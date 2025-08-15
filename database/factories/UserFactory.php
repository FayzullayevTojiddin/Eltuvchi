<?php

namespace Database\Factories;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'role' => 'client',
            'email' => $this->faker->unique()->safeEmail(),
            'telegram_id' => $this->faker->unique()->randomNumber(9, true),
            'password' => Hash::make('password'),
        ];
    }

    public function asClient(): static
    {
        return $this->state(fn () => ['role' => 'client']);
    }

    public function asDriver(): static
    {
        return $this->state(fn () => ['role' => 'driver']);
    }

    public function driver()
    {
        return $this->has(Driver::factory());
    }
}