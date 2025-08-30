<?php

namespace App\Filament\Resources\Dispatchers\Pages;

use App\Filament\Resources\Dispatchers\DispatcherResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDispatcher extends ViewRecord
{
    protected static string $resource = DispatcherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
