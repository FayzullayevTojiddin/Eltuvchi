<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $stats = Client::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN status != 'active' THEN 1 ELSE 0 END) as inactive
        ")->first();

        return [
            Stat::make('Umumiy mijozlar soni', $stats->total)
                ->icon('heroicon-o-users')  
                ->color('primary'),

            Stat::make('Active mijozlar soni', $stats->active)
                ->icon('heroicon-o-user-circle')
                ->color('success'),

            Stat::make('NoFaol mijozlar soni', $stats->inactive)
                ->icon('heroicon-o-user-minus')
                ->color('danger'),
        ];
    }
}