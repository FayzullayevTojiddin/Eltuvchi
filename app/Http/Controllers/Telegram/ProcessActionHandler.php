<?php

namespace App\Http\Controllers\Telegram;

use Illuminate\Support\Facades\DB;

class ProcessActionHandler extends BaseTelegramController
{
    public function handler($chatId, $user)
    {
        if (!$user) {
            $this->sendMessage($chatId, "❌ Siz ro'yxatdan o'tmagansiz!\n\n👉 Iltimos /start buyrug'ini yuboring.");
            return;
        }

        $connected = $user->role === 'client' ? $user->client : $user->driver;
        
        if (!$connected) {
            $this->sendMessage($chatId, "❌ Hisob ma'lumotlari topilmadi.", $this->getMainKeyboard($user));
            return;
        }

        if ($connected->status !== 'new') {
            $this->sendMessage($chatId, "ℹ️ Hisobingiz allaqachon faollashtirilgan yoki faollashtirish mumkin emas.", $this->getMainKeyboard($user));
            return;
        }

        $subscribePrice = env('SUBSCRIBE_PRICE', 0);
        $currentBalance = $connected->balance ?? 0;

        if ($currentBalance < $subscribePrice) {
            $text = "❌ Balans yetarli emas!\n\n";
            $text .= "Balansni to'ldiring va qaytadan urinib ko'ring.";
            $this->sendMessage($chatId, $text, $this->getMainKeyboard($user));
            return;
        }

        try {
            DB::beginTransaction();

            $paymentSuccess = $connected->subtractBalance($subscribePrice, 'Hisobni faollashtirish uchun obuna to\'lovi');

            if (!$paymentSuccess) {
                DB::rollBack();
                $text = "❌ To'lovda xatolik yuz berdi!\n\n";
                $text .= "Balans yetarli emas. Iltimos qayta urinib ko'ring.";
                $this->sendMessage($chatId, $text, $this->getMainKeyboard($user));
                return;
            }

            $connected->update(['status' => 'active']);

            DB::commit();
            
            $connected->refresh();

            $text = "🎉 Tabriklaymiz!\n\n";
            $text .= "✅ Hisobingiz muvaffaqiyatli faollashtirildi!\n\n";
            $text .= "💸 To'lov: " . number_format($subscribePrice, 0, '.', ' ') . " so'm\n";
            $text .= "💰 Yangi balans: " . number_format($connected->balance, 0, '.', ' ') . " so'm\n\n";
            $text .= "🚀 Endi barcha xizmatlardan to'liq foydalanishingiz mumkin.\n\n";
            $text .= "💼 Botimiz imkoniyatlaridan bahramand bo'ling!";

        } catch (\Exception $e) {
            DB::rollBack();
            
            $text = "❌ Xatolik yuz berdi!\n\n";
            $text .= "Iltimos, qaytadan urinib ko'ring yoki admin bilan bog'laning.\n";
            $text .= "👤 @" . env('TELEGRAM_ADMIN_USERNAME', 'admin');
        }
        
        $this->sendMessage($chatId, $text, $this->getMainKeyboard($user));
    }
}
