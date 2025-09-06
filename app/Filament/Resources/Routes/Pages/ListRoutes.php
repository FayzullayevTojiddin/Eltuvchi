<?php

namespace App\Filament\Resources\Routes\Pages;

use App\Filament\Resources\Routes\RouteResource;
use App\Filament\Widgets\RoutesOverview;
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
            CreateAction::make()->label("Yangi Yo'l Yaratish"),
        ];
    }
}
