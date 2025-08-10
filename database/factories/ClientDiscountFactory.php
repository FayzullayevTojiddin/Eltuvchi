<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Discount;
use App\Models\ClientDiscount;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientDiscountFactory extends Factory
{
    protected $model = ClientDiscount::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'discount_id' => Discount::factory(),
            'used' => false,
        ];
    }
}