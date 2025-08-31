<?php

namespace App\Filament\Resources\Drivers\Pages;

use App\Filament\Actions\DisActiveAction;
use App\Filament\Actions\SendMessageAction;
use App\Filament\Resources\Drivers\DriverResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDriver extends EditRecord
{
    protected static string $resource = DriverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label("O'chirish"),
            DisActiveAction::create(),
            SendMessageAction::create()
        ];
    }
}
