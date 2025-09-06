<?php

namespace App\Filament\Resources\Regions\Pages;

use App\Filament\Resources\Regions\RegionResource;
use App\Filament\Widgets\RegionsOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRegions extends ListRecords
{
    protected static string $resource = RegionResource::class;

    protected static ?string $title = "Regionlar";

    protected function getHeaderWidgets(): array
    {
        return [
            RegionsOverview::class
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label("Yangi Region Yaratish"),
        ];
    }
}
