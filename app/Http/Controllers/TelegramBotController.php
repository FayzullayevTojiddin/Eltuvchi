<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Telegram\StartHandler;
use Illuminate\Http\Request;
use Telegram\Bot\Api;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
                        $startHandler = new StartHandler();
                        $startHandler->handler($chatId, $user);
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
            Log::error('Telegram Bot Error: ' . $e->getMessage());
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function handleCallbackQuery($chatId, $data, $user, $callbackQueryId)
    {
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
            
            case 'confirm_activate':
                $this->processActivation($chatId, $user);
                break;
            
            case 'cancel_activate':
                $this->sendMessage($chatId, "❌ Faollashtirish bekor qilindi.", $this->getMainKeyboard($user));
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

        $connected = $user->role === 'client' ? $user->client : $user->driver;
        
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
                $text .= "💳 Balansni to'ldirish uchun 'Balans 💰' bo'limiga o'ting.";
                
                $this->sendMessage($chatId, $text, $this->getMainKeyboard($user));
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

    private function processActivation($chatId, $user)
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
            Log::error('Activation payment error: ' . $e->getMessage());
            
            $text = "❌ Xatolik yuz berdi!\n\n";
            $text .= "Iltimos, qaytadan urinib ko'ring yoki admin bilan bog'laning.\n";
            $text .= "👤 @" . env('TELEGRAM_ADMIN_USERNAME', 'admin');
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

            if ($needsActivation) {
                $keyboard[] = ['Faollashtirish ✅'];
            } elseif ($isBlocked) {
                $keyboard[] = ['Blokdan chiqish 🔓'];
            }
        }

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