<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Api;
use App\Models\User;

class TelegramBotController extends Controller
{
    protected $telegram;

    public function __construct()
    {
        $this->telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
    }

    public function handle(Request $request)
    {
        try {
            $update = $this->telegram->getWebhookUpdate();
            
            if ($update->getMessage()) {
                $message = $update->getMessage();
                $chatId = $message->getChat()->getId();
                $text = $message->getText();
                $telegramUserId = $message->getFrom()->getId();

                $user = User::where('telegram_id', $telegramUserId)->first();

                switch ($text) {
                    case '/start':
                        $this->sendWelcomeMessage($chatId, $user);
                        break;
                    
                    case 'Balans 💰':
                        $this->sendBalance($chatId, $user);
                        break;
                    
                    case 'Hisobim 👤':
                        $this->sendProfile($chatId, $user);
                        break;
                    
                    case 'Faollashtirish ✅':
                        $this->activateAccount($chatId, $user);
                        break;
                    
                    default:
                        $this->sendMessage($chatId, "Noma'lum buyruq. /start bosing.", $this->getMainKeyboard($user));
                        break;
                }
            }

            return response()->json(['ok' => true]);
            
        } catch (\Exception $e) {
            \Log::error('Telegram Bot Error: ' . $e->getMessage());
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function sendWelcomeMessage($chatId, $user)
    {
        $text = "Assalomu alaykum!\n\n";
        $text .= "Xush kelibsiz 🎉\n\n";
        $text .= "Quyidagi tugmalardan foydalaning:";
        
        $this->sendMessage($chatId, $text, $this->getMainKeyboard($user));
    }

    private function sendBalance($chatId, $user)
    {
        if (!$user) {
            $this->sendMessage($chatId, "❌ Siz ro'yxatdan o'tmagansiz!");
            return;
        }

        $balance = $user->balance ?? 0;
        $text = "💰 Sizning balansingiz:\n\n";
        $text .= number_format($balance, 2, '.', ' ') . " so'm";
        
        $this->sendMessage($chatId, $text, $this->getMainKeyboard($user));
    }

    private function sendProfile($chatId, $user)
    {
        if (!$user) {
            $this->sendMessage($chatId, "❌ Siz ro'yxatdan o'tmagansiz!");
            return;
        }

        $status = 'unknown';
        $statusText = '❓ Noma\'lum';

        if ($user->client) {
            $status = $user->client->status ?? 'new';
        } elseif ($user->driver) {
            $status = $user->driver->status ?? 'new';
        }

        if ($status === 'active') {
            $statusText = '✅ Faol';
        } elseif ($status === 'new') {
            $statusText = '🆕 Yangi';
        } elseif ($status === 'inactive') {
            $statusText = '❌ Faol emas';
        }
        
        $text = "👤 Hisobingiz ma'lumotlari:\n\n";
        $text .= "📝 Ism: " . ($user->name ?? 'Belgilanmagan') . "\n";
        $text .= "📞 Telefon: " . ($user->phone ?? 'Belgilanmagan') . "\n";
        $text .= "🔰 Status: " . $statusText . "\n";
        $text .= "💰 Balans: " . number_format($user->balance ?? 0, 2, '.', ' ') . " so'm";
        
        $this->sendMessage($chatId, $text, $this->getMainKeyboard($user));
    }

    private function activateAccount($chatId, $user)
    {
        if (!$user) {
            $this->sendMessage($chatId, "❌ Siz ro'yxatdan o'tmagansiz!");
            return;
        }

        $activated = false;

        if ($user->client && $user->client->status === 'new') {
            $user->client->update(['status' => 'active']);
            $activated = true;
        }
        
        if ($user->driver && $user->driver->status === 'new') {
            $user->driver->update(['status' => 'active']);
            $activated = true;
        }

        if ($activated) {
            $text = "✅ Hisobingiz muvaffaqiyatli faollashtirildi!\n\n";
            $text .= "Endi barcha xizmatlardan foydalanishingiz mumkin 🎉";
        } else {
            $text = "ℹ️ Hisobingiz allaqachon faol yoki faollashtirishga muhtoj emas.";
        }
        
        $this->sendMessage($chatId, $text, $this->getMainKeyboard($user));
    }

    private function getMainKeyboard($user)
    {
        $keyboard = [
            ['Balans 💰', 'Hisobim 👤'],
        ];

        if ($user) {
            $needsActivation = false;

            if ($user->client && $user->client->status === 'new') {
                $needsActivation = true;
            }
            if ($user->driver && $user->driver->status === 'new') {
                $needsActivation = true;
            }

            if ($needsActivation) {
                $keyboard[] = ['Faollashtirish ✅'];
            }
        }

        return [
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => false,
        ];
    }

    private function sendMessage($chatId, $text, $keyboard = null)
    {
        $params = [
            'chat_id' => $chatId,
            'text' => $text,
        ];

        if ($keyboard) {
            $params['reply_markup'] = json_encode($keyboard);
        }

        $this->telegram->sendMessage($params);
    }
}