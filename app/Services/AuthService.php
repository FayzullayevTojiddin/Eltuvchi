<?php

namespace App\Services;

use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthService
{
    public function handleTelegramAuth(string $initData)
    {
        $userData = dd($this->validateTelegramInitData($initData));

        if (!$userData) {
            return response()->json(['message' => 'Invalid Telegram init data'], 401);
        }

        $telegramId = $userData['id'];

        $user = User::where('telegram_id', $telegramId)->first();

        if (!$user) {
            DB::beginTransaction();
            try {
                $user = User::create([
                    'telegram_id' => $telegramId,
                    'email' => null,
                    'password' => null,
                    'role' => 'client',
                ]);

                Client::create([
                    'user_id' => $user->id,
                    'status' => 'active',
                    'balance' => 0,
                    'points' => 0,
                    'settings' => [],
                ]);

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error('Telegram Auth Error', ['error' => $e->getMessage()]);
                return response()->json(['message' => 'Server error'], 500);
            }
        }

        $token = $user->createToken('telegram-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'role' => $user->role,
            'user_id' => $user->id,
        ]);
    }

    private function validateTelegramInitData(string $initData): ?array
    {
        parse_str($initData, $data);

        $hash = $data['hash'] ?? null;
        if (!$hash) return null;

        // hash va signature ni olib tashlaymiz
        unset($data['hash'], $data['signature']);

        // keylar bo‘yicha saralash
        ksort($data);

        // key=value formatda \n bilan birlashtirish
        $checkString = implode("\n", array_map(fn($k, $v) => "$k=$v", array_keys($data), $data));

        // HMAC-SHA256 hash hisoblash
        $secretKey = hash('sha256', env('TELEGRAM_BOT_TOKEN'), true);
        $calculatedHash = hash_hmac('sha256', $checkString, $secretKey);

        // Solishtirish
        if (hash_equals($calculatedHash, $hash)) {
            return $data; // Haqiqiy foydalanuvchi
        }

        return null; // Noto‘g‘ri initData
    }
}