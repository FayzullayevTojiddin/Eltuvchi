<?php

namespace App\Filament\Widgets;

use App\Models\TaxoPark;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TaxoParksOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $total     = TaxoPark::count();
        $active    = TaxoPark::where('status', 'active')->count();
        $disactive = TaxoPark::where('status', 'disactive')->count();

        return [
            Stat::make('Jami Taxoparklar', $total)
                ->description('Barchasi')
                ->descriptionIcon('heroicon-o-building-office')
                ->color('primary'),

            Stat::make('Faol Taxoparklar', $active)
                ->description('Ishlayotgan taxoparklar')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Nofaol Taxoparklar', $disactive)
                ->description('Hozir faol emas')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color('danger'),
        ];
    }
}
