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

                if (!$user && $text === '/start') {
                    $user = $this->createNewUser($tgUser, $telegramUserId);
                }

                switch ($text) {
                    case '/start':
                        $this->sendWelcomeMessage($chatId, $user);
                        break;
                    
                    case '/balance':
                    case 'Balans 💰':
                        $this->sendBalance($chatId, $user);
                        break;
                    
                    case '/my':
                    case 'Hisobim 👤':
                        $this->sendProfile($chatId, $user);
                        break;
                    
                    case 'Faollashtirish ✅':
                        $this->activateAccount($chatId, $user);
                        break;
                    
                    case 'Blokdan chiqish 🔓':
                        $this->requestUnblock($chatId, $user);
                        break;
                    
                    default:
                        if ($user) {
                            $this->sendMessage($chatId, "❓ Noma'lum buyruq.\n\nQuyidagi tugmalardan foydalaning yoki /start buyrug'ini yuboring.", $this->getMainKeyboard($user));
                        } else {
                            $this->sendMessage($chatId, "👋 Botdan foydalanish uchun /start buyrug'ini yuboring.");
                        }
                        break;
                }
            } elseif ($update->getCallbackQuery()) {
                $callbackQuery = $update->getCallbackQuery();
                $chatId = $callbackQuery->getMessage()->getChat()->getId();
                $data = $callbackQuery->getData();
                $telegramUserId = $callbackQuery->getFrom()->getId();

                $user = User::where('telegram_id', $telegramUserId)->first();

                $this->handleCallbackQuery($chatId, $data, $user, $callbackQuery->getId());
            }

            return response()->json(['ok' => true]);
            
        } catch (\Exception $e) {
            \Log::error('Telegram Bot Error: ' . $e->getMessage());
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function handleCallbackQuery($chatId, $data, $user, $callbackQueryId)
    {
        // Callback query javobini yuborish
        $this->telegram->answerCallbackQuery([
            'callback_query_id' => $callbackQueryId,
        ]);

        if (!$user) {
            $this->sendMessage($chatId, "❌ Siz ro'yxatdan o'tmagansiz!\n\n👉 Iltimos /start buyrug'ini yuboring.");
            return;
        }

        switch ($data) {
            case 'deposit':
                $this->handleDeposit($chatId, $user);
                break;
            
            case 'withdraw':
                $this->handleWithdraw($chatId, $user);
                break;
            
            default:
                $this->sendMessage($chatId, "❓ Noma'lum amal.");
                break;
        }
    }

    private function handleDeposit($chatId, $user)
    {
        $text = "💳 Pul kiritish\n\n";
        $text .= "Hisobingizga pul kiritish uchun quyidagi ma'lumotlardan foydalaning:\n\n";
        $text .= "📌 Karta raqami: 8600 1234 5678 9012\n";
        $text .= "📌 Qabul qiluvchi: TaxiService LLC\n\n";
        $text .= "💡 Pul o'tkazgandan so'ng admin bilan bog'laning:\n";
        $text .= "👤 @" . env('TELEGRAM_ADMIN_USERNAME', 'admin') . "\n\n";
        $text .= "⚠️ To'lov tasdiqlanishi 10-30 daqiqa ichida amalga oshiriladi.";
        
        $this->sendMessage($chatId, $text, $this->getMainKeyboard($user));
    }

    private function handleWithdraw($chatId, $user)
    {
        $connected = $user->role === 'client' ? $user->client : $user->driver;
        $balance = $connected->balance ?? 0;
        
        $text = "💸 Pul chiqarish\n\n";
        $text .= "💰 Mavjud balans: " . number_format($balance, 0, '.', ' ') . " so'm\n\n";
        $text .= "Pul chiqarish uchun admin bilan bog'laning:\n";
        $text .= "👤 @" . env('TELEGRAM_ADMIN_USERNAME', 'admin') . "\n\n";
        $text .= "📝 Adminga quyidagilarni yuboring:\n";
        $text .= "• Chiqarmoqchi bo'lgan summa\n";
        $text .= "• Karta raqami\n";
        $text .= "• Karta egasi ismi\n\n";
        $text .= "⏱ Pul 24 soat ichida o'tkaziladi.";
        
        $this->sendMessage($chatId, $text, $this->getMainKeyboard($user));
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
        
        $text = "🌟 Assalomu alaykum";
        
        if ($isNewUser) {
            $text .= ", " . ($user->name ?? 'Foydalanuvchi') . "!\n\n";
            $text .= "🎊 Xush kelibsiz! Siz muvaffaqiyatli ro'yxatdan o'tdingiz.\n\n";
            $text .= "✨ Botimizdan to'liq foydalanish uchun hisobingizni faollashtiring.\n\n";
            $text .= "👇 Quyidagi 'Faollashtirish ✅' tugmasini bosing.";
        } else {
            $text .= ", " . ($user->name ?? 'Foydalanuvchi') . "!\n\n";
            $text .= "🚀 Botimizga xush kelibsiz!\n\n";
            $text .= "📱 Quyidagi menyudan kerakli bo'limni tanlang:";
        }
        
        $this->sendMessage($chatId, $text, $this->getMainKeyboard($user));
    }

    private function sendBalance($chatId, $user)
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

        $balance = $connected->balance ?? 0;
        
        $text = "💰 Sizning balansingiz:\n\n";
        $text .= "💵 " . number_format($balance, 0, '.', ' ') . " so'm\n\n";
        
        // Oxirgi 3 ta tranzaksiya
        $histories = $connected->balanceHistories()
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();
        
        if ($histories->count() > 0) {
            $text .= "📊 Oxirgi tranzaksiyalar:\n\n";
            
            foreach ($histories as $history) {
                $type = $history->type === 'credit' ? '➕' : '➖';
                $amount = number_format(abs($history->amount), 0, '.', ' ');
                $date = $history->created_at->format('d.m.Y H:i');
                
                $text .= "{$type} {$amount} so'm\n";
                $text .= "📝 {$history->description}\n";
                $text .= "🕐 {$date}\n\n";
            }
        } else {
            $text .= "ℹ️ Hozircha tranzaksiyalar yo'q.";
        }
        
        // Inline tugmalar
        $inlineKeyboard = [
            [
                ['text' => '💳 Pul kiritish', 'callback_data' => 'deposit'],
                ['text' => '💸 Pul chiqarish', 'callback_data' => 'withdraw']
            ]
        ];
        
        $this->sendMessage($chatId, $text, $this->getMainKeyboard($user), $inlineKeyboard);
    }

    private function sendProfile($chatId, $user)
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

        $status = $connected->status ?? 'new';
        $statusText = '❓ Noma\'lum';

        if ($status === 'active') {
            $statusText = '✅ Faol';
        } elseif ($status === 'new') {
            $statusText = '🆕 Yangi (Faollashtirishni kutmoqda)';
        } elseif ($status === 'inactive') {
            $statusText = '🚫 Bloklangan';
        }
        
        // Buyurtmalar soni
        $ordersCount = 0;
        if ($user->role === 'client') {
            $ordersCount = $user->client->orders()->count();
        } elseif ($user->role === 'driver') {
            $ordersCount = $user->driver->orders()->count();
        }
        
        $roleText = $user->role === 'client' ? 'Mijoz' : 'Haydovchi';
        $idText = $user->role === 'client' ? 'Client ID' : 'Driver ID';
        $connectedId = $connected->id ?? 'N/A';
        
        $text = "👤 Shaxsiy kabinetingiz\n\n";
        $text .= "━━━━━━━━━━━━━━━━━━\n\n";
        $text .= "👨‍💼 Ism: " . ($user->name ?? 'Belgilanmagan') . "\n";
        $text .= "🆔 User ID: {$user->id}\n";
        $text .= "🔖 {$idText}: {$connectedId}\n";
        $text .= "👥 Rol: {$roleText}\n";
        $text .= "📞 Telefon: " . ($user->phone ?? 'Belgilanmagan') . "\n";
        $text .= "🔰 Status: {$statusText}\n";
        $text .= "💰 Balans: " . number_format($connected->balance ?? 0, 0, '.', ' ') . " so'm\n";
        $text .= "📦 Umumiy buyurtmalar: {$ordersCount} ta\n";
        $text .= "📅 Ro'yxatdan o'tgan: " . $user->created_at->format('d.m.Y H:i') . "\n\n";
        $text .= "━━━━━━━━━━━━━━━━━━";
        
        $this->sendMessage($chatId, $text, $this->getMainKeyboard($user));
    }

    private function activateAccount($chatId, $user)
    {
        if (!$user) {
            $this->sendMessage($chatId, "❌ Siz ro'yxatdan o'tmagansiz!\n\n👉 Iltimos /start buyrug'ini yuboring.");
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
            $text = "🎉 Tabriklaymiz!\n\n";
            $text .= "✅ Hisobingiz muvaffaqiyatli faollashtirildi!\n\n";
            $text .= "🚀 Endi barcha xizmatlardan to'liq foydalanishingiz mumkin.\n\n";
            $text .= "💼 Botimiz imkoniyatlaridan bahramand bo'ling!";
        } else {
            $text = "ℹ️ Hisobingiz allaqachon faol holda.";
        }
        
        $this->sendMessage($chatId, $text, $this->getMainKeyboard($user));
    }

    private function requestUnblock($chatId, $user)
    {
        if (!$user) {
            $this->sendMessage($chatId, "❌ Siz ro'yxatdan o'tmagansiz!\n\n👉 Iltimos /start buyrug'ini yuboring.");
            return;
        }

        $adminUsername = env('TELEGRAM_ADMIN_USERNAME', 'admin');
        
        $text = "🔓 Blokdan chiqish so'rovi\n\n";
        $text .= "📞 Hisobingizni qayta faollashtirish uchun admin bilan bog'laning:\n\n";
        $text .= "👤 @{$adminUsername}\n\n";
        $text .= "💬 Adminga o'zingizni taqdim qiling va blokdan chiqish sababini tushuntiring.";
        
        $this->sendMessage($chatId, $text, $this->getMainKeyboard($user));
    }

    private function getMainKeyboard($user)
    {
        $keyboard = [];

        if ($user) {
            $needsActivation = false;
            $isBlocked = false;

            if ($user->client) {
                if ($user->client->status === 'new') {
                    $needsActivation = true;
                } elseif ($user->client->status === 'inactive') {
                    $isBlocked = true;
                }
            }
            
            if ($user->driver) {
                if ($user->driver->status === 'new') {
                    $needsActivation = true;
                } elseif ($user->driver->status === 'inactive') {
                    $isBlocked = true;
                }
            }

            // Birinchi qatorda faqat faollashtirish yoki blokdan chiqish
            if ($needsActivation) {
                $keyboard[] = ['Faollashtirish ✅'];
            } elseif ($isBlocked) {
                $keyboard[] = ['Blokdan chiqish 🔓'];
            }
        }

        // Asosiy tugmalar
        $keyboard[] = ['Balans 💰', 'Hisobim 👤'];

        return [
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => false,
        ];
    }

    private function sendMessage($chatId, $text, $keyboard = null, $inlineKeyboard = null)
    {
        $params = [
            'chat_id' => $chatId,
            'text' => $text,
        ];

        if ($inlineKeyboard) {
            $params['reply_markup'] = json_encode([
                'inline_keyboard' => $inlineKeyboard
            ]);
        } elseif ($keyboard) {
            $params['reply_markup'] = json_encode($keyboard);
        }

        $this->telegram->sendMessage($params);
    }
}