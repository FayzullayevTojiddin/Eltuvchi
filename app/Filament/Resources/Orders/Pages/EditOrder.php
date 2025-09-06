<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Actions\AddOrderHistoryAction;
use App\Filament\Actions\SendMessageAction;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected static ?string $title = "Buyurtmani Tahrirlash";

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label("O'chirish"),
            ActionGroup::make([
                SendMessageAction::create(fn($record) => $record->driver->user->telegram_id, 'messageToDriver')->label("Taxiga yozish"),
                SendMessageAction::create(fn($record) => $record->client->user->telegram_id, 'messageToClient')->label("Clientga yozish"),
                AddOrderHistoryAction::make()
            ])
        ];
    }
}