<?php

namespace App\Events;

use App\Traits\TelegramBotTrait;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class OrderChangedSendMessageEvent
{
    use Dispatchable, SerializesModels, TelegramBotTrait;

    public User $user;
    public string $message;

    public function __construct(User $user, string $message)
    {
        $this->user = $user;
        $this->message = $message;
        $this->sendTelegramMessage($this->user->telegram_id, $this->message);
    }
}