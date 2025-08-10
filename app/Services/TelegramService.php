<?php

namespace App\Services;

class TelegramService
{
    public static function validateInitData(string $initData, string $botToken): ?array
    {
        parse_str($initData, $data);

        if (isset($data['user']) && is_string($data['user'])) {
            $data['user'] = json_decode($data['user'], true);
        }

        return $data;
    }
}
