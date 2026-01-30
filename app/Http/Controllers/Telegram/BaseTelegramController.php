<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Telegram\Bot\Api;

class BaseTelegramController extends Controller
{
    protected $telegram;

    public function __construct()
    {
        $this->telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
    }

    public function sendMessage($chatId, $text, $keyboard = null, $inlineKeyboard = null)
    {
        $params = [
            'chat_id' => $chatId,
            'text' => $text,
        ];

        if ($inlineKeyboard) {
            $params['reply_markup'] = json_encode([
                'inline_keyboard' => $inlineKeyboard
            ]);
        } elseif ($keyboard) {
            $params['reply_markup'] = json_encode($keyboard);
        }

        $this->telegram->sendMessage($params);
    }

    public function getMainKeyboard($user)
    {
        $keyboard = [];

        if ($user) {
            $needsActivation = false;
            $isBlocked = false;

            if ($user->connected()) {
                if ($user->connected->status === 'new') {
                    $needsActivation = true;
                } elseif ($user->connected->status === 'inactive') {
                    $isBlocked = true;
                }
            }

            if ($needsActivation) {
                $keyboard[] = ['Faollashtirish ✅'];
            } elseif ($isBlocked) {
                $keyboard[] = ['Blokdan chiqish 🔓'];
            }
        }

        $keyboard[] = ['Balans 💰', 'Hisobim 👤'];

        if($user->role === 'client' and !$user->driver) {
            $keyboard[] = ['🚕 Taxi bo‘lish'];
        }

        return [
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => false,
        ];
    }
}
