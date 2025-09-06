<?php

namespace App\Filament\Widgets;

use App\Models\Discount;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DiscountsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $stats = Discount::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN status != 'active' THEN 1 ELSE 0 END) as inactive
        ")->first();

        return [
            Stat::make('Umumiy chegirmalar soni', $stats->total)
                // ->icon('heroicon-o-users')  
                ->color('primary'),

            Stat::make('Active chegirmalar soni', $stats->active)
                // ->icon('heroicon-o-user-circle')
                ->color('success'),

            Stat::make('Bloklangan chegirmalar soni', $stats->inactive)
                // ->icon('heroicon-o-user-minus')
                ->color('danger'),
        ];
    }
}
