<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Actions\SendMessageAction;
use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label("O'chirish"),
            ActionGroup::make([
                SendMessageAction::create(fn($record) => $record->driver?->user?->telegram_id)->label("Taxiga yozish"),
                SendMessageAction::create(fn($record) => $record->client?->user?->telegram_id)->label("Clientga yozish"),
            ])
        ];
    }
}