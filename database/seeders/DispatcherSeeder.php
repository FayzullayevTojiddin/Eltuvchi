<?php

namespace Database\Seeders;

use App\Models\Dispatcher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DispatcherSeeder extends Seeder
{
    public function run(): void
    {
        Dispatcher::factory(50)->create();
    }
}
