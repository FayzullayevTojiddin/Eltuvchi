<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Api;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;

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
                $tgUser = $message->getFrom();

                $user = User::where('telegram_id', $telegramUserId)->first();

                // Agar user topilmasa va /start bosilsa - yangi yaratish
                if (!$user && $text === '/start') {
                    $user = $this->createNewUser($tgUser, $telegramUserId);
                }

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
                        if ($user) {
                            $this->sendMessage($chatId, "Noma'lum buyruq. /start bosing.", $this->getMainKeyboard($user));
                        } else {
                            $this->sendMessage($chatId, "Iltimos, /start bosing.");
                        }
                        break;
                }
            }

            return response()->json(['ok' => true]);
            
        } catch (\Exception $e) {
            \Log::error('Telegram Bot Error: ' . $e->getMessage());
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function createNewUser($tgUser, string $telegramId): User
    {
        $user = User::create([
            'name' => trim($tgUser->getFirstName() ?? 'Telegram User'),
            'telegram_id' => $telegramId,
            'username' => $tgUser->getUsername() ?? null,
            'role' => 'client',
            'password' => Hash::make(uniqid('tg_', true)),
        ]);

        $settings = [
            'full_name' => trim(
                ($tgUser->getFirstName() ?? '') . ' ' . ($tgUser->getLastName() ?? '')
            ),
            'phone_number' => null,
            'notifications' => true,
            'night_mode' => false,
            'language' => $tgUser->getLanguageCode() ?? 'uz',
        ];

        Client::create([
            'user_id' => $user->id,
            'status' => 'new',
            'balance' => 0,
            'points' => 0,
            'settings' => $settings,
        ]);

        return $user;
    }

    private function sendWelcomeMessage($chatId, $user)
    {
        $isNewUser = $user && $user->client && $user->client->status === 'new';
        
        $text = "Assalomu alaykum";
        
        if ($isNewUser) {
            $text .= ", " . ($user->name ?? 'Foydalanuvchi') . "!\n\n";
            $text .= "🎉 Siz muvaffaqiyatli ro'yxatdan o'tdingiz!\n\n";
            $text .= "Hisobingizni faollashtirish uchun 'Faollashtirish ✅' tugmasini bosing.";
        } else {
            $text .= "!\n\nXush kelibsiz 🎉\n\n";
            $text .= "Quyidagi tugmalardan foydalaning:";
        }
        
        $this->sendMessage($chatId, $text, $this->getMainKeyboard($user));
    }

    private function sendBalance($chatId, $user)
    {
        if (!$user) {
            $this->sendMessage($chatId, "❌ Siz ro'yxatdan o'tmagansiz!\n\nIltimos /start bosing.");
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
            $this->sendMessage($chatId, "❌ Siz ro'yxatdan o'tmagansiz!\n\nIltimos /start bosing.");
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
            $statusText = '🆕 Yangi (Faollashtirishni kutmoqda)';
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
            $this->sendMessage($chatId, "❌ Siz ro'yxatdan o'tmagansiz!\n\nIltimos /start bosing.");
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
            $text = "✅ Tabriklaymiz!\n\n";
            $text .= "Hisobingiz muvaffaqiyatli faollashtirildi! 🎉\n\n";
            $text .= "Endi barcha xizmatlardan to'liq foydalanishingiz mumkin.";
        } else {
            $text = "ℹ️ Hisobingiz allaqachon faol.";
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