<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

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
            OrderSeeder::class,
        ]);

        User::factory()->create(['role' => 'superadmin', 'email' => 'super@gmail.com', 'password' => bcrypt('1')]);
        User::factory()->create(['role' => 'taxoparkadmin', 'email' => 'taxopakr@gmail.com', 'password' => bcrypt('1')]);
    }
}