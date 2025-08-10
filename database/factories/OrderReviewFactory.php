<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Order;
use App\Models\OrderReview;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderReviewFactory extends Factory
{
    protected $model = OrderReview::class;

    public function definition()
    {
        return [
            'order_id' => Order::factory(),
            'client_id' => Client::factory(),
            'score' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->sentence(),
        ];
    }
}