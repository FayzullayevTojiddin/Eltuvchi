<?php

namespace App\Http\Controllers\Telegram;

use App\Models\Region;

class RouteToTaxiHandler extends BaseTelegramController
{
    public function handler($chatId, $user)
    {
        $user->update([
            'telegram_state' => 'choosing_taxi_region'
        ]);

        $regions = Region::where('status', true)
            ->orderBy('name')
            ->get();

        $keyboard = [];

        foreach ($regions->chunk(2) as $chunk) {
            $row = [];
            foreach ($chunk as $region) {
                $row[] = '📍 ' . $region->name;
            }
            $keyboard[] = $row;
        }

        $keyboard[] = ['⬅️ Orqaga'];

        $this->sendMessage($chatId, [
            'text' => "🚕 *Taxi bo‘lish uchun kerakli regionni tanlang:*",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'keyboard' => $keyboard,
                'resize_keyboard' => true,
                'one_time_keyboard' => false
            ])
        ]);
    }
}