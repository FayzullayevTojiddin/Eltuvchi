<?php

namespace App\Http\Controllers\Telegram;

class DepositHandler extends BaseTelegramController
{
    public function handler($chatId, $user)
    {
        $text = "💳 Pul kiritish\n\n";
        $text .= "Hisobingizga pul kiritish uchun quyidagi ma'lumotlardan foydalaning:\n\n";
        $text .= "📌 Karta raqami: 8600 1234 5678 9012\n";
        $text .= "📌 Qabul qiluvchi: TaxiService LLC\n\n";
        $text .= "💡 Pul o'tkazgandan so'ng admin bilan bog'laning:\n";
        $text .= "👤 @" . env('TELEGRAM_ADMIN_USERNAME', 'admin') . "\n\n";
        $text .= "⚠️ To'lov tasdiqlanishi 10-30 daqiqa ichida amalga oshiriladi.";
        
        $this->sendMessage($chatId, $text, $this->getMainKeyboard($user));
    }
}
