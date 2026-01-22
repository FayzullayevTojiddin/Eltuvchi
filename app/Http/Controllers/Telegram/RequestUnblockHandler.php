<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RequestUnblockHandler extends BaseTelegramController
{
    public function handler($chatId, $user)
    {
        if (!$user) {
            $this->sendMessage($chatId, "❌ Siz ro'yxatdan o'tmagansiz!\n\n👉 Iltimos /start buyrug'ini yuboring.");
            return;
        }

        $adminUsername = env('TELEGRAM_ADMIN_USERNAME', 'admin');
        
        $text = "🔓 Blokdan chiqish so'rovi\n\n";
        $text .= "📞 Hisobingizni qayta faollashtirish uchun admin bilan bog'laning:\n\n";
        $text .= "👤 @{$adminUsername}\n\n";
        $text .= "💬 Adminga o'zingizni taqdim qiling va blokdan chiqish sababini tushuntiring.";
        
        $this->sendMessage($chatId, $text, $this->getMainKeyboard($user));
    }
}
