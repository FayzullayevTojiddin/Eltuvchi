<?php

namespace App\Filament\Widgets;

use App\Models\ClientDiscount;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DiscountOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $total = ClientDiscount::count();
        $used = ClientDiscount::where('used', true)->count();
        $unused = ClientDiscount::where('used', false)->count();

        return [
            Stat::make('Jami berilgan chegirmalar', $total)
                ->description('Barcha client-discount yozuvlari')
                ->color('primary'),

            Stat::make('Ishlatilgan chegirmalar', $used)
                ->description('Foydalanilganlar')
                ->color('success'),

            Stat::make('Ishlatilmagan chegirmalar', $unused)
                ->description('Foydalanilmaganlar')
                ->color('danger'),
        ];
    }
}