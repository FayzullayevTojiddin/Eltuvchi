<?php

namespace Database\Factories;

use App\Models\PointHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PointHistoryFactory extends Factory
{
    protected $model = PointHistory::class;

    public function definition()
    {
        $superAdminIds = User::where('role', 'superadmin')->pluck('id');
        return [
            'user_id' => $superAdminIds->isNotEmpty() ? $superAdminIds->random() : User::factory()->create(['role' => 'superadmin'])->id,
            'points' => $this->faker->numberBetween(1, 100),
            'type' => $this->faker->randomElement(['plus', 'minus']),
            'points_after' => $this->faker->numberBetween(1, 100),
            'description' => $this->faker->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}