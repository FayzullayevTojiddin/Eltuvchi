<?php

namespace Database\Seeders;

use App\Models\Region;
use App\Models\TaxoPark;
use Illuminate\Database\Seeder;

class TaxoParkSeeder extends Seeder
{
    public function run(): void
    {
        $regions = Region::all();
        foreach($regions as $region) {
            TaxoPark::factory(random_int(1, 2))->create([
                'region_id' => $region->id,
            ]);
        }
    }
}
