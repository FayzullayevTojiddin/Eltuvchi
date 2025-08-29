<?php

namespace Database\Seeders;

use App\Models\Region;
use App\Models\TaxoPark;
use Illuminate\Database\Seeder;

class TaxoParkSeeder extends Seeder
{
    public function run(): void
    {
        $regions = Region::pluck('id');

        $data = $regions->flatMap(function ($regionId) {
            return TaxoPark::factory()
                ->count(random_int(1, 2))
                ->make([
                    'region_id' => $regionId,
                ]);
        });

        TaxoPark::insert($data->toArray());
    }
}