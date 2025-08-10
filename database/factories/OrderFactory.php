<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Client;
use App\Models\Driver;
use App\Models\Order;
use App\Models\Route;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $passengers = $this->faker->numberBetween(1, 5);
        $priceIn = 100000;
        $total = $priceIn * $passengers;
        $discount = $this->faker->numberBetween(0, min(50000, $total));
        $deposit = (int) (($total - $discount) * 0.2);

        return [
            'client_id' => Client::factory(),
            'driver_id' => Driver::factory(),
            'route_id' => Route::factory(),
            'passengers' => 1,
            'date' => now()->format('Y-m-d'),
            'time' => now()->format('H:i'),
            'price_order' => 100000,
            'client_deposit' => 50000,
            'discount_percent' => 0,
            'discount_summ' => 0,
            'phone' => '998901234567',
            'optional_phone' => null,
            'note' => null,
            'status' => OrderStatus::Created->value,
        ];
    }
}