<?php

namespace App\Filament\TaxoParkAdmin\Resources\Drivers\Pages;

use App\Filament\TaxoParkAdmin\Resources\Drivers\DriverResource;
use App\Filament\TaxoParkAdmin\Widgets\DriversOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDrivers extends ListRecords
{
    protected static string $resource = DriverResource::class;

    protected static ?string $title = "Haydovchilar ro'yxati";

    protected function getHeaderWidgets(): array
    {
        return [
            DriversOverview::class
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label("Yangi Haydovchi Qo'shish"),
        ];
    }
}
