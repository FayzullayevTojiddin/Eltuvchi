<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RegionSeeder::class,
            TaxoParkSeeder::class,
            RouteSeeder::class,
            ClientSeeder::class,
            DriverSeeder::class,
            DiscountSeeder::class,
        ]);
    }
}
