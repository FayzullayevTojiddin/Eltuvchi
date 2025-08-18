<?php

namespace Database\Seeders;

use App\Models\Discount;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DiscountSeeder extends Seeder
{
    public function run(): void
    {
        Discount::create([
            'type' => 'percent',
            'value' => 10,
            'points' => 200,
            'title' => '10% chegirma',
            'icon' => '🎁',
        ]);
        Discount::create([
            'type' => 'percent',
            'value' => 15,
            'points' => 500,
            'title' => '15% chegirma',
            'icon' => '💸',
        ]);
    }
}
