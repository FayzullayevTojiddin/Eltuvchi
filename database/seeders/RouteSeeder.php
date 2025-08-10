<?php

namespace Database\Seeders;

use App\Models\Route;
use App\Models\TaxoPark;
use Exception;
use Illuminate\Database\Seeder;

class RouteSeeder extends Seeder
{
    public function run(): void
    {
        $taxoparks = TaxoPark::all();
        foreach ($taxoparks as $from) {
            foreach ($taxoparks as $to) {
                if ($from->id === $to->id) {
                    continue;
                }
                try {
                    $exists = Route::where('taxopark_from_id', $from->id)
                        ->where('taxopark_to_id', $to->id)
                        ->exists();
                    if ($exists) {
                        continue;
                    }
                    Route::create([
                        'taxopark_from_id' => $from->id,
                        'taxopark_to_id' => $to->id,
                        'status' => 'active',
                        'deposit_client' => rand(50000, 150000),
                        'distance_km' => rand(10, 100),
                        'price_in' => rand(10000, 20000),
                    ]);
                } catch (Exception $e) {
                    continue;
                }
            }
        }
    }
}