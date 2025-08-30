<?php

namespace App\Filament\Actions;

use App\Traits\TelegramBotTrait;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;

class SendMessageAction
{
    use TelegramBotTrait;

    public static function create($chatId = null): Action
    {
        return Action::make('sendMessage')
            ->label('Xabar Yuborish')
            ->icon('heroicon-o-paper-airplane')
            ->form([
                Textarea::make('message')
                    ->label('Message')
                    ->required(),
            ])
            ->action(function ($record, array $data) use ($chatId) {
                (new self())->sendTelegramMessage($record->user->telegram_id, $data['message']);
            });
    }
}