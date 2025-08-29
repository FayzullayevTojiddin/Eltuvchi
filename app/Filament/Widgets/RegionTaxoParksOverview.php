<?php

namespace App\Filament\Widgets;

use App\Models\Region;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RegionTaxoParksOverview extends StatsOverviewWidget
{
    public ?Region $record = null;

    protected function getStats(): array
    {
        $region = $this->record->load('taxoparks');
        $total     = $region->taxoparks->count();
        $active    = $region->taxoparks->where('status', 'active')->count();
        $disactive = $region->taxoparks->where('status', 'disactive')->count();

        return [
            Stat::make('Jami Taxoparklar', $total)
                ->description($region->name)
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
