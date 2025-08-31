<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\Product;
use App\Models\DriverProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DriverProductSeeder extends Seeder
{
    public function run(): void
    {
        $drivers = Driver::all();

        $products = Product::factory()->count($drivers->count())->create();

        $inserts = [];

        foreach ($drivers->values() as $index => $driver) {
            $inserts[] = [
                'driver_id' => $driver->id,
                'product_id' => $products[$index]->id,
                'delivered' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('driver_products')->insert($inserts);
    }
}