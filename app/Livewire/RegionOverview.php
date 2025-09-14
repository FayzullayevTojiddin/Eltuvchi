<?php

namespace App\Livewire;

use App\Models\Region;
use App\Models\TaxoPark;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RegionOverview extends StatsOverviewWidget
{
    public ?Region $record = null;

    protected function getStats(): array
    {
        $total = TaxoPark::where('region_id', $this->record->id)->count();
        $used = TaxoPark::where('region_id', $this->record->id)->where('status', 'active')->count();
        $unused = TaxoPark::where('region_id', $this->record->id)->where('status', 'inactive')->count();

        return [
            Stat::make('Jami TaxoParklar', $total)
                ->description('Barcha region-taxopark yozuvlari')
                ->color('primary'),

            Stat::make('Faol TaxoParklar', $used)
                ->description('Faollari')
                ->color('success'),

            Stat::make('NoFaol TaxoParklar', $unused)
                ->description('bloklanglar')
                ->color('danger'),
        ];
    }
}
