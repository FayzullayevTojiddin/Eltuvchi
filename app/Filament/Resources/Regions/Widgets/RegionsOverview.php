<?php

namespace App\Filament\Resources\Regions\Widgets;

use App\Models\Region;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RegionsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Jami regionlar', Region::count())
                ->description('Umumiy regionlar soni')
                ->color('primary'),
            
            Stat::make('Active regionlar', Region::where('status', 'active')->count())
                ->description('Faol regionlar soni')
                ->color('success'),

            Stat::make('Disactive regionlar', Region::where('status', 'disactive')->count())
                ->description('Nofaol regionlar soni')
                ->color('danger'),
        ];
    }
}
