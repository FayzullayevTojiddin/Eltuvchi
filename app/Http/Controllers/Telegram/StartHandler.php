<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Telegram\Bot\Api;

class StartHandler extends BaseTelegramController
{
    
    public function handler($chatId, $user)
    {
        $isNewUser = $user && $user->client && $user->client->status === 'new';
        $userName = $user['settings']['full_name'];
        $text = "🌟 Assalomu alaykum";
        
        if ($isNewUser) {
            $text .= ", " . ($userName ?? 'Foydalanuvchi') . "!\n\n";
            $text .= "🎊 Xush kelibsiz! Siz muvaffaqiyatli ro'yxatdan o'tdingiz.\n\n";
            $text .= "✨ Botimizdan to'liq foydalanish uchun hisobingizni faollashtiring.\n\n";
            $text .= "👇 Quyidagi 'Faollashtirish ✅' tugmasini bosing.";
        } else {
            $text .= ", " . ($userName ?? 'Foydalanuvchi') . "!\n\n";
            $text .= "🚀 Botimizga xush kelibsiz!\n\n";
            $text .= "📱 Quyidagi menyudan kerakli bo'limni tanlang:";
        }
        
        $this->sendMessage($chatId, $text, $this->getMainKeyboard($user));
    }
}
