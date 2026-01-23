<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Telegram\ActivateAccountHandler;
use App\Http\Controllers\Telegram\BalanceHandler;
use App\Http\Controllers\Telegram\BaseTelegramController;
use App\Http\Controllers\Telegram\DepositHandler;
use App\Http\Controllers\Telegram\ProcessActionHandler;
use App\Http\Controllers\Telegram\ProfileHandler;
use App\Http\Controllers\Telegram\RequestUnblockHandler;
use App\Http\Controllers\Telegram\StartHandler;
use App\Http\Controllers\Telegram\WidthdrawHandler;
use Illuminate\Http\Request;
use Telegram\Bot\Api;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class TelegramBotController extends BaseTelegramController
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

            if ($update->getCallbackQuery()) {
                $callbackQuery = $update->getCallbackQuery();
                $chatId = $callbackQuery->getMessage()->getChat()->getId();
                $data = $callbackQuery->getData();
                $telegramUserId = $callbackQuery->getFrom()->getId();

                $user = User::where('telegram_id', $telegramUserId)->first();

                $this->handleCallbackQuery(
                    $chatId,
                    $data,
                    $user,
                    $callbackQuery->getId()
                );

                return response()->json(['ok' => true]);
            }

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

                // ✅ YANGI: Agar foydalanuvchi deposit summasini kutayotgan bo'lsa
                if ($user && $user->telegram_state === 'waiting_deposit_amount') {
                    if ($text === '❌ Bekor qilish') {
                        $user->update(['telegram_state' => null]);
                        $this->sendMessage($chatId, "❌ Bekor qilindi.", $this->getMainKeyboard($user));
                        return response()->json(['ok' => true]);
                    }
                    
                    $depositHandler = new DepositHandler();
                    $depositHandler->handleAmount($chatId, $user, $text);
                    return response()->json(['ok' => true]);
                }

                switch ($text) {
                    case '/start':
                        (new StartHandler())->handler($chatId, $user);
                        break;

                    case '/balance':
                    case 'Balans 💰':
                        (new BalanceHandler())->handler($chatId, $user);
                        break;

                    case '/my':
                    case 'Hisobim 👤':
                        (new ProfileHandler())->handler($chatId, $user);
                        break;

                    case 'Faollashtirish ✅':
                        (new ActivateAccountHandler())->handler($chatId, $user);
                        break;

                    case 'Blokdan chiqish 🔓':
                        (new RequestUnblockHandler())->handler($chatId, $user);
                        break;

                    default:
                        if ($user) {
                            $this->sendMessage(
                                $chatId,
                                "❓ Noma'lum buyruq.\n\nQuyidagi tugmalardan foydalaning yoki /start buyrug'ini yuboring.",
                                $this->getMainKeyboard($user)
                            );
                        } else {
                            $this->sendMessage(
                                $chatId,
                                "👋 Botdan foydalanish uchun /start buyrug'ini yuboring."
                            );
                        }
                }
            }

            return response()->json(['ok' => true]);

        } catch (\Throwable $e) {
            Log::error('Telegram Bot Error: ' . $e->getMessage());

            return response()->json([
                'ok' => false,
                'error' => $e->getMessage()
            ], 500);
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
                $depositHandler = new DepositHandler();
                $depositHandler->handler($chatId, $user);
                break;
            
            case 'withdraw':
                $withdrawalHandler = new WidthdrawHandler();
                $withdrawalHandler->handler($chatId, $user);
                break;
            
            case 'confirm_activate':
                $processActivationHandler = new ProcessActionHandler();
                $processActivationHandler->handler($chatId, $user);
                break;
            
            case 'cancel_activate':
                $this->sendMessage($chatId, "❌ Faollashtirish bekor qilindi.", $this->getMainKeyboard($user));
                break;
            
            // ✅ YANGI: Bosh menyuga qaytish
            case 'main_menu':
                $user->update(['telegram_state' => null]);
                $this->sendMessage($chatId, "🏠 Bosh menyu", $this->getMainKeyboard($user));
                break;
            
            default:
                $this->sendMessage($chatId, "❓ Noma'lum amal.");
                break;
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
}