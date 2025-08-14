<?php

namespace Database\Seeders;

use App\Enums\RouteStatus;
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
                    Route::factory()->create([
                        'taxopark_from_id' => $from->id,
                        'taxopark_to_id' => $to->id,
                        'status' => RouteStatus::ACTIVE->value
                    ]);
                } catch (Exception $e) {
                    continue;
                }
            }
        }
    }
}