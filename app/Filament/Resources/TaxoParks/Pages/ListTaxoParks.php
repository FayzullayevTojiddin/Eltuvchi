<?php

namespace App\Filament\Resources\TaxoParks\Pages;

use App\Filament\Resources\TaxoParks\TaxoParkResource;
use App\Filament\Widgets\RegionTaxoParksOverview;
use App\Filament\Widgets\TaxoParksOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTaxoParks extends ListRecords
{
    protected static string $resource = TaxoParkResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            TaxoParksOverview::class
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
