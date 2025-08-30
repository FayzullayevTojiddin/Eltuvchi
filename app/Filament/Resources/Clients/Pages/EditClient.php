<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Actions\DisActiveAction;
use App\Filament\Actions\SendMessageAction;
use App\Filament\Resources\Clients\ClientResource;
use App\Traits\TelegramBotTrait;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\EditRecord;

class EditClient extends EditRecord
{
    use TelegramBotTrait;

    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label("O'chirish"),
            DisActiveAction::create(),
            SendMessageAction::create($this->record->user->telegram_id)
        ];
    }
}
