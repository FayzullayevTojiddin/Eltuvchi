<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProductsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $all = Product::count();
        $active = Product::where('status', true)->count();
        $inactive = Product::where('status', false)->count();

        return [
            Stat::make('Barcha Mahsulotlar', $all)
                ->description('Umumiy soni')
                ->color('primary'),

            Stat::make('Aktiv Mahsulotlar', $active)
                ->description('Hozir aktiv')
                ->color('success'),

            Stat::make('Deaktiv Mahsulotlar', $inactive)
                ->description('O‘chirib qo‘yilgan')
                ->color('danger'),
        ];
    }
}