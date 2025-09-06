<?php

namespace App\Filament\Widgets;

use App\Models\Route;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RoutesOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $stats = Route::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN status != 'active' THEN 1 ELSE 0 END) as inactive
        ")->first();

        return [
            Stat::make("Umumiy yo'llar soni", $stats->total)
                // ->icon('heroicon-o-users')  
                ->color('primary'),

            Stat::make("Active yo'llar soni", $stats->active)
                // ->icon('heroicon-o-user-circle')
                ->color('success'),

            Stat::make("Bloklangan yo'llar soni", $stats->inactive)
                // ->icon('heroicon-o-user-minus')
                ->color('danger'),
        ];
    }
}
