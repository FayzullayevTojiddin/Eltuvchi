<?php

namespace App\Support;

class OrderTemp
{
    public static ?string $description = null;

    public static function set(?string $desc): void
    {
        self::$description = $desc;
    }

    public static function pull(): ?string
    {
        $desc = self::$description;
        self::$description = null;
        return $desc;
    }
}