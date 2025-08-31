<?php

namespace App\Filament\Resources\Drivers\Pages;

use App\Filament\Resources\Drivers\DriverResource;
use App\Filament\Widgets\DriversOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDrivers extends ListRecords
{
    protected static string $resource = DriverResource::class;

    protected static ?string $title = "Haydovchilar";

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label("Haydovchini Yaratish"),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DriversOverview::class
        ];
    }
}
