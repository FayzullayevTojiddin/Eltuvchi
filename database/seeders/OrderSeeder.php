<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\TaxoPark;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $taxoparks = TaxoPark::all();
        $orders = [];

        foreach ($taxoparks as $taxopark) {
            $routeIds = $taxopark->routesFrom()->pluck('id')
                ->merge($taxopark->routesTo()->pluck('id'))
                ->values();

            if ($routeIds->isEmpty()) {
                continue;
            }

            $batch = Order::factory()
                ->count(50)
                ->make([
                    'route_id' => $routeIds->random(),
                ])
                ->toArray();

            $orders = array_merge($orders, $batch);
        }
        DB::table('orders')->insert($orders);
    }
}