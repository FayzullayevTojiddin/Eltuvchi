<?php

namespace App\Http\Controllers\Telegram;

use App\Models\DepositRequest;
use App\Services\ClickService;

class DepositHandler extends BaseTelegramController
{
    protected $clickService;

    public function __construct()
    {
        parent::__construct();
        $this->clickService = new ClickService();
    }

    public function handler($chatId, $user)
    {
        $user->update(['telegram_state' => 'waiting_deposit_amount']);
        
        $text = "💳 Pul kiritish\n\n";
        $text .= "💰 To'lamoqchi bo'lgan summangizni kiriting (so'mda):\n\n";
        $text .= "📝 Misol: 50000, 100000, 200000\n\n";
        $text .= "⚠️ Minimal summa: 20,000 so'm\n";
        $text .= "⚠️ Maksimal summa: 10,000,000 so'm";
        
        $this->sendMessage($chatId, $text, $this->getCancelKeyboard());
    }

    public function handleAmount($chatId, $user, $amount)
    {
        $amount = (int) str_replace([' ', ',', '.'], '', $amount);
        
        if ($amount < 20000) {
            $this->sendMessage($chatId, "❌ Minimal summa 20,000 so'm bo'lishi kerak!");
            return;
        }
        
        if ($amount > 10000000) {
            $this->sendMessage($chatId, "❌ Maksimal summa 10,000,000 so'm!");
            return;
        }
        
        $processingText = "⏳ Chek yaratilmoqda...\n\nIltimos, kuting...";
        $removeKeyboard = [
            'remove_keyboard' => true
        ];
        $this->sendMessage($chatId, $processingText, $removeKeyboard);
        
        try {
            $invoice = $this->clickService->createInvoice($user->id, $amount);
            
            $text = "✅ To'lov havolasi yaratildi!\n\n";
            $text .= "💰 Summa: " . number_format($amount, 0, '.', ' ') . " so'm\n";
            $text .= "🔗 To'lov uchun quyidagi tugmani bosing:\n\n";
            $text .= "⚠️ To'lov amalga oshgandan keyin avtomatik ravishda hisobingizga o'tadi.\n";
            $text .= "⏱ Bu 1-2 daqiqa vaqt olishi mumkin.";
            
            $user->update(['telegram_state' => null]);
            
            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => '💳 To\'lovni amalga oshirish', 'url' => $invoice['url']]
                    ],
                    [
                        ['text' => '🏠 Bosh menyu', 'callback_data' => 'main_menu']
                    ]
                ]
            ];
            
            $this->sendMessage($chatId, $text, $keyboard);
        } catch (\Exception $e) {
            $this->sendMessage(
                $chatId,
                "❌ To'lov havolasini yaratishda xatolik yuz berdi.\n\nIltimos, qayta urinib ko'ring yoki admin bilan bog'laning: @" . env('TELEGRAM_ADMIN_USERNAME', 'admin'),
                $this->getMainKeyboard($user)
            );
        }
    }

    private function getCancelKeyboard()
    {
        return [
            'keyboard' => [
                [['text' => "20000"], ['text' => "50000"]],
                [['text' => "100000"], ['text' => "200000"]],
                [['text' => '❌ Bekor qilish']],
            ],
            'resize_keyboard' => true,
        ];
    }
}