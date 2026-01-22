<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class TelegramBotController extends Controller
{
    public function handle(Request $request)
    {
        $update = $request->all();

        if (isset($update['message']['text'])) {
            $text = $update['message']['text'];
            $chatId = $update['message']['chat']['id'];
            $userId = $update['message']['from']['id'];

            // Foydalanuvchini topish
            $user = User::where('telegram_id', $userId)->first();

            if ($text === '/start') {
                $this->sendWelcomeMessage($chatId, $user);
            } elseif ($text === 'Balans 💰') {
                $this->sendBalance($chatId, $user);
            } elseif ($text === 'Hisobim 👤') {
                $this->sendProfile($chatId, $user);
            } elseif ($text === 'Faollashtirish ✅') {
                $this->activateAccount($chatId, $user);
            }
        }

        return response()->json(['ok' => true]);
    }

    private function sendWelcomeMessage($chatId, $user)
    {
        $text = "Assalomu alaykum!\n\nXush kelibsiz 🎉";
        
        $keyboard = $this->getMainKeyboard($user);
        
        $this->sendMessage($chatId, $text, $keyboard);
    }

    private function sendBalance($chatId, $user)
    {
        if (!$user) {
            $this->sendMessage($chatId, "Siz ro'yxatdan o'tmagansiz!");
            return;
        }

        $balance = $user->balance ?? 0;
        $text = "💰 Sizning balansingiz: " . number_format($balance, 2) . " so'm";
        
        $this->sendMessage($chatId, $text, $this->getMainKeyboard($user));
    }

    private function sendProfile($chatId, $user)
    {
        if (!$user) {
            $this->sendMessage($chatId, "Siz ro'yxatdan o'tmagansiz!");
            return;
        }

        $status = $user->connected->status ?? 'unknown';
        $statusText = $status === 'active' ? '✅ Faol' : '❌ Faol emas';
        
        $text = "👤 Hisobingiz ma'lumotlari:\n\n";
        $text .= "Ism: " . ($user->name ?? 'Belgilanmagan') . "\n";
        $text .= "Telefon: " . ($user->phone ?? 'Belgilanmagan') . "\n";
        $text .= "Status: " . $statusText . "\n";
        $text .= "Balans: " . number_format($user->balance ?? 0, 2) . " so'm";
        
        $this->sendMessage($chatId, $text, $this->getMainKeyboard($user));
    }

    private function activateAccount($chatId, $user)
    {
        if (!$user) {
            $this->sendMessage($chatId, "Siz ro'yxatdan o'tmagansiz!");
            return;
        }

        if ($user->connected && $user->connected->status === 'new') {
            $user->connected->update(['status' => 'active']);
            
            $text = "✅ Hisobingiz muvaffaqiyatli faollashtirildi!\n\nEndi barcha xizmatlardan foydalanishingiz mumkin.";
        } else {
            $text = "Hisobingiz allaqachon faol ✅";
        }
        
        $this->sendMessage($chatId, $text, $this->getMainKeyboard($user));
    }

    private function getMainKeyboard($user)
    {
        $keyboard = [
            ['Balans 💰', 'Hisobim 👤'],
        ];

        if ($user && $user->connected && $user->connected->status === 'new') {
            $keyboard[] = ['Faollashtirish ✅'];
        }

        return [
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => false
        ];
    }

    private function sendMessage($chatId, $text, $keyboard = null)
    {
        $token = config('services.telegram.bot_token');
        $url = "https://api.telegram.org/bot{$token}/sendMessage";

        $data = [
            'chat_id' => $chatId,
            'text' => $text
        ];

        if ($keyboard) {
            $data['reply_markup'] = json_encode($keyboard);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }
}