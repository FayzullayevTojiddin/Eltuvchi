<?php

namespace Database\Seeders;

use App\Enums\RouteStatus;
use App\Models\Route;
use App\Models\TaxoPark;
use Illuminate\Database\Seeder;

class RouteSeeder extends Seeder
{
    public function run(): void
    {
        $taxoparks = TaxoPark::pluck('id');
        $existing = Route::select('taxopark_from_id', 'taxopark_to_id')
            ->get()
            ->map(fn ($r) => $r->taxopark_from_id . '-' . $r->taxopark_to_id)
            ->toArray();
        $data = collect();
        foreach ($taxoparks as $from) {
            foreach ($taxoparks as $to) {
                if ($from === $to) {
                    continue;
                }

                $key = $from . '-' . $to;
                if (in_array($key, $existing, true)) {
                    continue;
                }

                $data->push(
                    Route::factory()->make([
                        'taxopark_from_id' => $from,
                        'taxopark_to_id'   => $to,
                        'status'           => RouteStatus::ACTIVE->value,
                    ])
                );
            }
        }

        if ($data->isNotEmpty()) {
            Route::insert($data->toArray());
        }
    }
}