<?php

namespace Database\Seeders;

use App\Models\Dispatcher;
use App\Models\TaxoPark;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DispatcherSeeder extends Seeder
{
    public function run(): void
    {
        $taxoParks = TaxoPark::pluck('id');
        Dispatcher::factory()
            ->count(50)
            ->state(fn () => [
                'taxopark_id' => $taxoParks->random(),
            ])
            ->create();
    }
}
