<?php

namespace App\Filament\Resources\Dispatchers\Pages;

use App\Filament\Resources\Dispatchers\DispatcherResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDispatcher extends EditRecord
{
    protected static string $resource = DispatcherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
