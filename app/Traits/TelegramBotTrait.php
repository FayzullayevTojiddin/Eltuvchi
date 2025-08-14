<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait TelegramBotTrait
{
    protected function getTelegramBotToken(): string
    {
        return config('services.telegram.bot_token');
    }

    public function sendTelegramMessage(string|int $chatId, string $message): bool
    {
        $token = $this->getTelegramBotToken();
        $url = "https://api.telegram.org/bot{$token}/sendMessage";
        $response = Http::post($url, [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
        ]);

        return $response->successful();
    }
}