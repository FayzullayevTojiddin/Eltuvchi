<?php

namespace App\Http\Controllers\Telegram;

class WidthdrawHandler extends BaseTelegramController
{
    public function handler($chatId, $user)
    {
        $connected = $user->role === 'client' ? $user->client : $user->driver;
        $balance = $connected->balance ?? 0;
        
        $text = "💸 Pul chiqarish\n\n";
        $text .= "💰 Mavjud balans: " . number_format($balance, 0, '.', ' ') . " so'm\n\n";
        $text .= "Pul chiqarish uchun admin bilan bog'laning:\n";
        $text .= "👤 @" . env('TELEGRAM_ADMIN_USERNAME', 'admin') . "\n\n";
        $text .= "📝 Adminga quyidagilarni yuboring:\n";
        $text .= "• Chiqarmoqchi bo'lgan summa\n";
        $text .= "• Karta raqami\n";
        $text .= "• Karta egasi ismi\n\n";
        $text .= "⏱ Pul 24 soat ichida o'tkaziladi.";
        
        $this->sendMessage($chatId, $text, $this->getMainKeyboard($user));
    }
}
