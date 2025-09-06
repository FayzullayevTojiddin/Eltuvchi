<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\ClientDiscount;
use App\Models\Discount;
use Illuminate\Database\Seeder;

class ClientDiscountSeeder extends Seeder
{
    public function run(): void
    {
        $discounts = Discount::pluck('id');
        $clients = Client::pluck('id');
        ClientDiscount::factory()
            ->count(rand(100, 500))
            ->state(function () use ($discounts, $clients) {
                return [
                    'discount_id' => $discounts->random(),
                    'client_id'   => $clients->random(),
                ];
            })
            ->create();
    }
}
