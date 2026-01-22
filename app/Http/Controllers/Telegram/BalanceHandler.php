<?php

namespace App\Http\Controllers\Telegram;

class BalanceHandler extends BaseTelegramController
{
    public function handler($chatId, $user)
    {
        if (!$user) {
            $this->sendMessage($chatId, "❌ Siz ro'yxatdan o'tmagansiz!\n\n👉 Iltimos /start buyrug'ini yuboring.");
            return;
        }

        $connected = $user->connected;
        
        if (!$connected) {
            $this->sendMessage($chatId, "❌ Hisob ma'lumotlari topilmadi.", $this->getMainKeyboard($user));
            return;
        }

        $balance = $connected->balance ?? 0;
        
        $text = "💰 Sizning balansingiz:\n\n";
        $text .= "💵 " . number_format($balance, 0, '.', ' ') . " so'm\n\n";
        
        $histories = $connected->balanceHistories()
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();
        
        if ($histories->count() > 0) {
            $text .= "📊 Oxirgi tranzaksiyalar:\n\n";
            
            foreach ($histories as $history) {
                if ($history->type === 'plus') {
                    $typeIcon = '💚';
                    $typeText = 'Kirim';
                } else {
                    $typeIcon = '❌';
                    $typeText = 'Chiqim';
                }
                
                $amount = number_format(abs($history->amount), 0, '.', ' ');
                $date = $history->created_at->format('d.m.Y H:i');
                
                $text .= "{$typeIcon} {$typeText}: {$amount} so'm\n";
                $text .= "📝 {$history->description}\n";
                $text .= "🕐 {$date}\n\n";
            }
        } else {
            $text .= "ℹ️ Hozircha tranzaksiyalar yo'q.";
        }
        
        $inlineKeyboard = [
            [
                ['text' => '💳 Pul kiritish', 'callback_data' => 'deposit'],
                ['text' => '💸 Pul chiqarish', 'callback_data' => 'withdraw']
            ]
        ];
        
        $this->sendMessage($chatId, $text, $this->getMainKeyboard($user), $inlineKeyboard);
    }
}
