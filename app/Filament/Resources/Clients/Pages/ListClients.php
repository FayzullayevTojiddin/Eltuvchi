<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use App\Filament\Widgets\ClientsOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClients extends ListRecords
{
    protected static string $resource = ClientResource::class;

    protected static ?string $title = "Foydalanuvchilar";

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label("Yangi Mijoz Yaratish"),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ClientsOverview::class
        ];
    }
}
