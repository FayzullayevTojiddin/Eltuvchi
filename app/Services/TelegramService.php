<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class TelegramService
{
    /**
     * Telegram WebApp initData ni validatsiya qiladi
     * 
     * @param string $initData - Telegram WebApp dan kelgan initData
     * @param string $botToken - Bot token
     * @return array|null - Validatsiya muvaffaqiyatli bo'lsa array, aks holda null
     */
    public static function validateInitData(string $initData, string $botToken): ?array
    {
        try {
            parse_str($initData, $data);

            $hash = $data['hash'] ?? null;
            if (!$hash) {
                Log::warning('Telegram initData: hash topilmadi');
                return null;
            }

            unset($data['hash']);

            $dataCheckArr = [];
            foreach ($data as $key => $value) {
                $dataCheckArr[] = $key . '=' . $value;
            }
            sort($dataCheckArr);
            $dataCheckString = implode("\n", $dataCheckArr);

            $secretKey = hash_hmac('sha256', $botToken, 'WebAppData', true);

            $computedHash = hash_hmac('sha256', $dataCheckString, $secretKey);

            if (!hash_equals($computedHash, $hash)) {
                Log::warning('Telegram initData: hash mos kelmadi', [
                    'expected' => $computedHash,
                    'received' => $hash
                ]);
                return null;
            }

            $authDate = (int)($data['auth_date'] ?? 0);
            $currentTime = time();
            
            if ($authDate === 0) {
                Log::warning('Telegram initData: auth_date topilmadi');
                return null;
            }

            if ($currentTime - $authDate > 86400) {
                Log::warning('Telegram initData: auth_date eskirgan', [
                    'auth_date' => $authDate,
                    'current_time' => $currentTime,
                    'diff' => $currentTime - $authDate
                ]);
                return null;
            }

            if (isset($data['user']) && is_string($data['user'])) {
                $userData = json_decode($data['user'], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::error('Telegram initData: user JSON decode xatosi', [
                        'error' => json_last_error_msg()
                    ]);
                    return null;
                }
                $data['user'] = $userData;
            }

            return $data;

        } catch (\Exception $e) {
            Log::error('Telegram initData validatsiya xatosi: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
}