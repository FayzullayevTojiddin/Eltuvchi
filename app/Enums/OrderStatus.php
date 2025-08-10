<?php
namespace App\Enums;

enum OrderStatus: string
{
    case Created = 'created';
    case Accepted = 'accepted';
    case Started = 'started';
    case Stopped = 'stopped';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}