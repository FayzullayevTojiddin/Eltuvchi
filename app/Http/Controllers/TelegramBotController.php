<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramBotController extends Controller
{
    public function handle(Request $request)
    {
        $update = $request->all();

        if (isset($update['message']['text'])) {
            $text = $update['message']['text'];
            $chatId = $update['message']['chat']['id'];

            if ($text === '/start') {
                $this->sendMessage($chatId, "Salom! Bot ishlayapti ✅");
            }
        }

        return response()->json(['ok' => true]);
    }

    private function sendMessage($chatId, $text)
    {
        $token = config('services.telegram.bot_token');

        $url = "https://api.telegram.org/bot{$token}/sendMessage";

        file_get_contents($url . '?' . http_build_query([
            'chat_id' => $chatId,
            'text' => $text
        ]));
    }
}