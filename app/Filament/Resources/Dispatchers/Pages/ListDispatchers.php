<?php

namespace App\Filament\Resources\Dispatchers\Pages;

use App\Filament\Resources\Dispatchers\DispatcherResource;
use App\Filament\Widgets\DispatchersOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDispatchers extends ListRecords
{
    protected static string $resource = DispatcherResource::class;

    protected static ?string $title = "TaxoPark Adminlari";

    protected function getHeaderWidgets(): array
    {
        return [
            DispatchersOverview::class
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label("Yangi Admin Yaratish"),
        ];
    }
}
