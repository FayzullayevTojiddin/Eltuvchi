<?php

namespace App\Filament\Resources\Dispatchers\Pages;

use App\Filament\Resources\Dispatchers\DispatcherResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDispatchers extends ListRecords
{
    protected static string $resource = DispatcherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
