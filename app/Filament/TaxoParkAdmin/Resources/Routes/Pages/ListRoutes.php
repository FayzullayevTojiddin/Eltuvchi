<?php

namespace App\Filament\TaxoParkAdmin\Resources\Routes\Pages;

use App\Filament\TaxoParkAdmin\Resources\Routes\RouteResource;
use App\Filament\TaxoParkAdmin\Widgets\RoutesOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRoutes extends ListRecords
{
    protected static string $resource = RouteResource::class;

    protected static ?string $title = "Yo'llar Ro'yxati";

    protected function getHeaderWidgets(): array
    {
        return [
            RoutesOverview::class
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label("Yangi Yo'nalish Qo'shish"),
        ];
    }
}
