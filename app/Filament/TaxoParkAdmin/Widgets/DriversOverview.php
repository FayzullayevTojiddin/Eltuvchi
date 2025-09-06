<?php

namespace App\Filament\TaxoParkAdmin\Widgets;

use App\Models\Driver;
use Auth;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DriversOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $taxoParkId = Auth::user()->dispatcher->taxopark->id;
        $stats = Driver::where('taxopark_id', $taxoParkId)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN status != 'active' THEN 1 ELSE 0 END) as inactive
            ")
            ->first();

        return [
            Stat::make('Umumiy haydovchilar soni', $stats->total)
                ->icon('heroicon-o-users')  
                ->color('primary'),

            Stat::make('Active haydovchilar soni', $stats->active)
                ->icon('heroicon-o-user-circle')
                ->color('success'),

            Stat::make('Bloklangan haydovchilar soni', $stats->inactive)
                ->icon('heroicon-o-user-minus')
                ->color('danger'),
        ];
    }
}
