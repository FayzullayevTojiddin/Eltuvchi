<?php

namespace App\Filament\Actions;

use App\Traits\TelegramBotTrait;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;

class SendMessageAction
{
    use TelegramBotTrait;

    public static function create($chatId): Action
    {
        return Action::make('sendMessage')
            ->label('Xabar Yuborish')
            ->icon('heroicon-o-paper-airplane')
            ->form([
                Textarea::make('message')
                    ->label('Message')
                    ->required(),
            ])
            ->action(function (array $data) use ($chatId) {
                (new self())->sendTelegramMessage($chatId, $data['message']);
            });
    }
}