<?php

namespace Database\Factories;

use App\Models\Referral;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReferralFactory extends Factory
{
    protected $model = Referral::class;

    public function definition()
    {
        return [
            'referred_by' => User::factory()->create()->id,
            'user_id'     => User::factory()->create()->id,
            'promo_code'  => $this->faker->unique()->bothify('PROMO-####'),
            'created_at'  => now(),
            'updated_at'  => now(),
        ];
    }
}