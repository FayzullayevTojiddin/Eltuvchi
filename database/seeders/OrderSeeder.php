<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\TaxoPark;
use Carbon\Carbon;
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
                ->map(function ($order) {
                    $order['date'] = Carbon::parse($order['date'])->toDateString();
                    $order['time'] = Carbon::parse($order['time'])->format('H:i:s');
                    return $order;
                })
                ->toArray();
        }
        DB::table('orders')->insert($orders);
    }
}