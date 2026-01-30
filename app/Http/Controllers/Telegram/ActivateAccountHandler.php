<?php

namespace App\Http\Controllers\Telegram;

class ActivateAccountHandler extends BaseTelegramController
{
    public function handler($chatId, $user)
    {
        if (!$user) {
            $this->sendMessage($chatId, "❌ Siz ro'yxatdan o'tmagansiz!\n\n👉 Iltimos /start buyrug'ini yuboring.");
            return;
        }
        
        $connected = $user->connected();
        
        if (!$connected) {
            $this->sendMessage($chatId, "❌ Hisob ma'lumotlari topilmadi.", $this->getMainKeyboard($user));
            return;
        }
        
        if ($connected->status === 'active') {
            $text = "ℹ️ Hisobingiz allaqachon faol holda.";
            $this->sendMessage($chatId, $text, $this->getMainKeyboard($user));
            return;
        }
        
        if ($connected->status === 'new') {
            $subscribePrice = env('SUBSCRIBE_PRICE', 0);
            $currentBalance = $connected->balance ?? 0;
            
            if ($currentBalance < $subscribePrice) {
                $needed = $subscribePrice - $currentBalance;
                $text = "❌ Balans yetarli emas!\n\n";
                $text .= "💰 Joriy balans: " . number_format($currentBalance, 0, '.', ' ') . " so'm\n";
                $text .= "💵 Faollashtirish narxi: " . number_format($subscribePrice, 0, '.', ' ') . " so'm\n";
                $text .= "📊 Yetishmayotgan: " . number_format($needed, 0, '.', ' ') . " so'm\n\n";
                $text .= "💳 Balansni to'ldirish uchun quyidagi tugmani bosing:";
                
                $inlineKeyboard = [
                    [
                        ['text' => '🏠 Bosh menyu', 'callback_data' => 'main_menu']
                    ],
                    [
                        ['text' => '💳 Balansni to\'ldirish', 'callback_data' => 'deposit']
                    ]
                ];
                
                $this->sendMessage($chatId, $text, null, $inlineKeyboard);
                return;
            }
            
            $text = "⚠️ Hisobni faollashtirish\n\n";
            $text .= "💰 Joriy balans: " . number_format($currentBalance, 0, '.', ' ') . " so'm\n";
            $text .= "💸 To'lov summasi: " . number_format($subscribePrice, 0, '.', ' ') . " so'm\n";
            $text .= "💵 Qolgan balans: " . number_format($currentBalance - $subscribePrice, 0, '.', ' ') . " so'm\n\n";
            $text .= "❓ Hisobingizni faollashtirishni tasdiqlaysizmi?";
            
            $inlineKeyboard = [
                [
                    ['text' => '✅ Tasdiqlash', 'callback_data' => 'confirm_activate'],
                    ['text' => '❌ Bekor qilish', 'callback_data' => 'cancel_activate']
                ]
            ];
            
            $this->sendMessage($chatId, $text, null, $inlineKeyboard);
            return;
        }
        
        $text = "ℹ️ Hisobingizni faollashtirish imkonsiz. Admin bilan bog'laning.\n";
        $text .= "👤 @" . env('TELEGRAM_ADMIN_USERNAME', 'admin');
        
        $this->sendMessage($chatId, $text, $this->getMainKeyboard($user));
    }
}
