<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;
use DB;

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

        DB::table('regions')->insertOrIgnore(
            collect($regions)->map(fn($name) => ['name' => $name])->toArray()
        );
    }
}