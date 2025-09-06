<?php

namespace App\Filament\Widgets;

use App\Models\Dispatcher;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DispatchersOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '10s';
    
    protected function getStats(): array
    {
        $allCount = Dispatcher::count();
        $activeCount = Dispatcher::where('status', 'active')->count();
        $inactiveCount = Dispatcher::where('status', 'inactive')->count();

        return [
            Stat::make('Barcha TaxoParklar Admini', $allCount),
            Stat::make('Activ TaxoPark Adminlari', $activeCount),
            Stat::make('Bloklangan TaxoParkAdminlari', $inactiveCount),
        ];
    }
}
