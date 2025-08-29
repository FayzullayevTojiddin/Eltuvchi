<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\TaxoPark;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DriverSeeder extends Seeder
{
    public function run(): void
    {
        $drivers = [];
        $taxoparks = TaxoPark::all();

        foreach ($taxoparks as $taxopark) {
            $count = rand(1, 2);

            $newDrivers = Driver::factory()
                ->count($count)
                ->make([
                    'taxopark_id' => $taxopark->id,
                ])
                ->map(function($driver){
                    $driverArray = $driver->toArray();
                    $driverArray['settings'] = json_encode($driverArray['settings']);
                    $driverArray['details'] = json_encode($driverArray['details']);
                    return $driverArray;
                })
                ->toArray();

            $drivers = array_merge($drivers, $newDrivers);
        }

        DB::table('drivers')->insert($drivers);
    }
}