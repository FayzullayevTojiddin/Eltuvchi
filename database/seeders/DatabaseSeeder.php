<?php

namespace Database\Seeders;

use App\Models\SuperAdmin;
use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create(['role' => 'superadmin', 'email' => 'super@gmail.com', 'password' => bcrypt('1')]);
        SuperAdmin::factory()->create(['user_id' => $user->id, 'full_name' => "Tizim"]);
        User::factory()->create(['role' => 'taxoparkadmin', 'email' => 'taxopark@gmail.com', 'password' => bcrypt('1')]);
        

        $this->call([
            RegionSeeder::class,
            TaxoParkSeeder::class,
            RouteSeeder::class,
            ClientSeeder::class,
            DriverSeeder::class,
            DiscountSeeder::class,
            OrderSeeder::class,
            DispatcherSeeder::class,
            DriverProductSeeder::class,
            ClientDiscountSeeder::class
        ]);
    }
}