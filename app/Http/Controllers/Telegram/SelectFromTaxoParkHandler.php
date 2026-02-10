<?php

namespace App\Http\Controllers\Telegram;

use App\Models\TaxoPark;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class SelectFromTaxoParkHandler extends BaseTelegramController
{
    public function handler($text, $chatId)
    {
        $user = User::where('telegram_id', $chatId)->first();
        $taxoPark = TaxoPark::where('name', $text)->first();
        
        if(!$taxoPark) {
            $this->sendMessage($chatId, "🚫 Taxi Park topilmadi.\n\nIltimos, ro'yxatdan tanlang 👇");
            return;
        }

        $user->update(['telegram_state' => 'awaiting_driver_registration']);
        
        Cache::put("driver_register:taxopark:{$user->id}", $taxoPark->id, now()->addMinutes(15));

        $exampleMessage = "🚕 Haydovchi bo'lish uchun quyidagi ma'lumotlaringizni to'ldiring:\n\n";
        $exampleMessage .= "📋 <b>Misol uchun:</b>\n\n";
        $exampleMessage .= "Abdullayev Aziz Akramovich\n";
        $exampleMessage .= "+998 90 123 45 67\n";
        $exampleMessage .= "AB 1234567\n";
        $exampleMessage .= "01A777AA\n";
        $exampleMessage .= "Chevrolet Lacetti\n";
        $exampleMessage .= "5 yil\n\n";
        $exampleMessage .= "✍️ Endi siz o'z ma'lumotlaringizni yuqoridagi formatda yuboring.";

        $this->sendMessage(
            $chatId,
            $exampleMessage,
            $this->getDriverRegisterKeyboard()
        );
    }

    protected function getDriverRegisterKeyboard()
    {
        return [
            'keyboard' => [[['text' => '⬅️ Orqaga']]],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];
    }
}