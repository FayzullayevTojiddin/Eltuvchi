<?php

namespace App\Http\Controllers\Telegram;

class StartHandler extends BaseTelegramController
{
    
    public function handler($chatId, $user)
    {
        $isNewUser = $user->connected()->status === 'new';
        
        $text = "🌟 Assalomu alaykum, {$user->displayName}!\n\n";
        
        if ($isNewUser) {
            $text .= "🎊 Xush kelibsiz! Siz muvaffaqiyatli ro'yxatdan o'tdingiz.\n\n";
            $text .= "✨ Botimizdan to'liq foydalanish uchun hisobingizni faollashtiring.\n\n";
            $text .= "👇 Quyidagi 'Faollashtirish ✅' tugmasini bosing.";
        } else {
            $text .= "🚀 Botimizga xush kelibsiz!\n\n";
            $text .= "📱 Quyidagi menyudan kerakli bo'limni tanlang:";
        }
        
        $this->sendMessage($chatId, $text, $this->getMainKeyboard($user));
    }
}
