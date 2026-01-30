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

        $regions = Region::where('status', 'active')
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

        $this->sendMessage(
            $chatId,
            "🚕 *Taxi bo‘lish uchun kerakli regionni tanlang:*",
            [
                'keyboard' => $keyboard,
                'resize_keyboard' => true,
                'one_time_keyboard' => false,
                'parse_mode' => "markdown",
            ]
        );
    }
}