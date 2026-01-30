<?php

namespace App\Http\Controllers\Telegram;

class StartHandler extends BaseTelegramController
{
    
    public function handler($chatId, $user)
    {
        $isNewUser = $user && $user->client && $user->client->status === 'new';
        
        $displayName = match(true) {
            $user?->role === 'client' && $user->client 
                => $user->client->settings['full_name'] ?? 'Foydalanuvchi',
            $user?->role === 'driver' && $user->driver 
                => $user->driver->details['full_name'] ?? 'Foydalanuvchi',
            default => $user?->name ?? 'Foydalanuvchi',
        };
        
        $text = "🌟 Assalomu alaykum, {$displayName}!\n\n";
        
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
