<?php

namespace App\Filament\Widgets;

use App\Models\Region;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RegionsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $stats = Region::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN status != 'active' THEN 1 ELSE 0 END) as inactive
        ")->first();

        return [
            Stat::make('Umumiy Regionlar soni', $stats->total)
                ->icon('heroicon-o-users')  
                ->color('primary'),

            Stat::make('Active Regionlar soni', $stats->active)
                ->icon('heroicon-o-user-circle')
                ->color('success'),

            Stat::make('Bloklangan Regionlar soni', $stats->inactive)
                ->icon('heroicon-o-user-minus')
                ->color('danger'),
        ];
    }
}
