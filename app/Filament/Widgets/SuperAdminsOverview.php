<?php

namespace App\Filament\Widgets;

use App\Models\SuperAdmin;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SuperAdminsOverview extends StatsOverviewWidget
{

    protected ?string $pollingInterval = '10s';
    
    protected function getStats(): array
    {
        $stats = SuperAdmin::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN status != 'active' THEN 1 ELSE 0 END) as inactive
        ")->first();

        return [
            Stat::make('Umumiy Adminlar soni', $stats->total)
                ->icon('heroicon-o-users')  
                ->color('primary'),

            Stat::make('Active Adminlar soni', $stats->active)
                ->icon('heroicon-o-user-circle')
                ->color('success'),

            Stat::make('Bloklangan Adminlar soni', $stats->inactive)
                ->icon('heroicon-o-user-minus')
                ->color('danger'),
        ];
    }
}
