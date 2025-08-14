<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\TaxoPark;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DriverSeeder extends Seeder
{
    public function run(): void
    {
        $taxoparks = TaxoPark::all();
        foreach($taxoparks as $taxopark){
            Driver::factory()->count(rand(1, 2))->create([
                'taxopark_id' => $taxopark->id
            ]);
        }
    }
}
