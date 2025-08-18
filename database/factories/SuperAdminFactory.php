<?php

namespace Database\Factories;

use App\Models\SuperAdmin;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SuperAdminFactory extends Factory
{
    protected $model = SuperAdmin::class;

    public function definition(): array
    {
        return [
            'user_id'   => User::factory(),
            'full_name' => $this->faker->name(),
            'status'    => true,
        ];
    }
}