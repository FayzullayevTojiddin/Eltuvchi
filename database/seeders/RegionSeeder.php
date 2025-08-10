<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $regions = [
            'Qoraqalpogʻiston',
            'Andijon',
            'Buxoro',
            'Fargʻona',
            'Jizzax',
            'Xorazm',
            'Namangan',
            'Navoiy',
            'Qashqadaryo',
            'Samarqand',
            'Sirdaryo',
            'Surxondaryo',
            'Toshkent viloyati',
            'Toshkent shahri',
        ];

        foreach ($regions as $name) {
            Region::firstOrCreate(['name' => $name]);
        }
    }
}