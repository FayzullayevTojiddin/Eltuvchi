<?php

namespace App\Filament\Actions;

use App\Traits\TelegramBotTrait;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;

class SendMessageAction
{
    use TelegramBotTrait;

    public static function create($chatId = null, $name = 'sendMessage'): Action
    {
        return Action::make($name)
            ->button()
            ->label('Xabar Yuborish')
            ->icon('heroicon-o-paper-airplane')
            ->form([
                Textarea::make('message')
                    ->label('Message')
                    ->required(),
            ])
            ->action(function ($record, array $data, Action $action) use ($chatId) {
                $sent = (new self())->sendTelegramMessage($record->user->telegram_id, $data['message']);
                if ($sent) {
                    $action->successNotificationTitle('Xabar yuborildi ✅');
                } else {
                    $action->failureNotificationTitle('Xabar yuborilmadi ❌');
                }
            });
    }
}