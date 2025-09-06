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
        $passengers = $this->faker->numberBetween(1, 4);
        $priceIn = 100000;
        $total = $priceIn * $passengers;
        $discount = $this->faker->numberBetween(0, min(50000, $total));
        $deposit = (int) (($total - $discount) * 0.2);

        return [
            'client_id' =>  Client::inRandomOrder()->value('id'),
            'driver_id' =>  Driver::inRandomOrder()->value('id'),
            'route_id' =>  Route::inRandomOrder()->value('id'),
            'driver_payment' => $total * 0.1,
            'passengers' => $passengers,
            'date' => now()->format('Y-m-d'),
            'time' => now()->format('H:i'),
            'price_order' => $total,
            'client_deposit' => $total * 0.2,
            'discount_percent' => 0,
            'discount_summ' => 0,
            'phone' => '998901234567',
            'optional_phone' => null,
            'note' => null,
            'status' => OrderStatus::Created->value,
        ];
    }
}