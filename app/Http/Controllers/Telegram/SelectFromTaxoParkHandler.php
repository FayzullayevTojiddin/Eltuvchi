<?php

namespace App\Http\Controllers\Telegram;

use App\Models\TaxoPark;
use App\Models\User;

class SelectFromTaxoParkHandler extends BaseTelegramController
{
    public function handler($text, $chatId)
    {
        $user = User::where('telegram_id', $chatId)->first();
        $taxoPark = TaxoPark::where('name', $text)->first();
        
        if(!$taxoPark) {
            $this->sendMessage($chatId, "🚫 Taxi Park topilmadi.\n\nIltimos, ro‘yxatdan tanlang 👇");
            return;
        }

        $user->update(['telegram_state' => 'getting_datas']);
        $this->sendMessage(
            $chatId,
            "🚕 Haydovchi bo‘lish uchun quyidagi ma’lumotlaringizni to‘ldiring:\n\n"
            . "• To‘liq ism\n"
            . "• Telefon raqam\n"
            . "• Haydovchilik guvohnomasi (seriya va raqam)\n"
            . "• Avtomobil raqami\n"
            . "• Avtomobil nomi\n"
            . "• Ish tajribasi (yil)\n\n",
            $this->getDriverRegisterKeyboard()
        );
    }

    protected function getDriverRegisterKeyboard()
    {
        return [
            'keyboard' => [[['text' => '⬅️ Ortga']]],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];
    }
}
