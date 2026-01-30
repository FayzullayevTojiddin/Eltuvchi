<?php

namespace App\Http\Controllers\Telegram;

use App\Models\Region;
use App\Models\User;

class SetDriverHandler extends BaseTelegramController
{
    public function handler($text, $chatId)
    {
        $user = User::where('telegram_id', $chatId)->first();
        $region = Region::where('name', $text)->first();

        if (! $region) {
            $this->sendMessage($chatId, "🚫 Region topilmadi.\n\nIltimos, ro‘yxatdan tanlang 👇");
            return;
        }

        $taxoParks = $region->taxoParks()->where('status', 'active')->get();

        if ($taxoParks->isEmpty()) {
            $this->sendMessage( $chatId, "🚕 Ushbu regionda hozircha faol taxi parklar yo‘q.", $this->getMainKeyboard($user));
            return;
        }

        $user->update(['telegram_state' => 'choosing_taxi_park']);

        $keyboard = [];

        $keyboard[] = ['⬅️ Orqaga'];

        foreach ($taxoParks->chunk(2) as $chunk) {
            $row = [];
            foreach ($chunk as $park) {
                $row[] = $park->name;
            }
            $keyboard[] = $row;
        }

        $keyboard[] = ['⬅️ Orqaga'];

        $this->sendMessage($chatId, "🚕 Taxi parkni tanlang:", [ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
    }
}