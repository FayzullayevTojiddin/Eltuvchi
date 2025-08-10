<?php

namespace Database\Factories;

use App\Models\Route;
use App\Models\TaxoPark;
use App\Enums\RouteStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class RouteFactory extends Factory
{
    protected $model = Route::class;

    public function definition(): array
    {
        $priceIn = $this->faker->numberBetween(100000, 500000);
        $deposit = (int)($priceIn * 0.2); // 20% oldindan to'lov

        return [
            'taxopark_from_id' => TaxoPark::factory(),
            'taxopark_to_id' => TaxoPark::factory(),
            'status' => RouteStatus::ACTIVE->value,
            'deposit_client' => $deposit,
            'distance_km' => $this->faker->numberBetween(50, 500),
            'price_in' => $priceIn,
            'fee_per_client' => $this->faker->numberBetween(10000, 50000),
        ];
    }
}