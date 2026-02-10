<?php

namespace App\Http\Controllers\Telegram;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class GettingDatasHandler extends BaseTelegramController
{
    public function handler($text, $chatId)
    {
        $user = User::where('telegram_id', $chatId)->first();

        $taxoparkId = Cache::get("driver_register:taxopark:{$user->id}");

        if (!$taxoparkId) {
            $this->sendMessage(
                $chatId,
                "⏳ Sessiya muddati tugadi.\n\nIltimos, qaytadan Taxi Park tanlang.",
                $this->getMainKeyboard($user)
            );
            $user->update(['telegram_state' => null]);
            return;
        }

        $pattern = '/^
            (.+)\n
            (\+998\s?\d{2}\s?\d{3}\s?\d{2}\s?\d{2})\n
            ([A-Z]{2})\s?\d+\s?\d+\s?\d+\n
            ([A-Z0-9]{5,8})\n
            (.+)\n
            (\d+)
            \s*(yil)?
        $/x';

        if (!preg_match($pattern, trim($text), $matches)) {
            $this->sendMessage($chatId, "❌ Ma'lumotlar noto'g'ri formatda kiritildi.\n\nIltimos, barcha ma'lumotlarni to'liq va to'g'ri kiriting.");
            return;
        }

        [
            $_,
            $fullName,
            $phone,
            $licenseSeries,
            $licenseNumber,
            $vehicleNumber,
            $vehicleName,
            $experience
        ] = $matches;

        $user->driver()->create([
            'status' => 'new',
            'taxopark_id' => $taxoparkId,
            'details' => [
                'full_name'        => $fullName,
                'phone_number'     => $phone,
                'license_series'   => $licenseSeries,
                'license_number'   => $licenseNumber,
                'vehicle_number'   => $vehicleNumber,
                'vehicle_name'     => $vehicleName,
                'experience_years' => (int) $experience,
            ],
            'settings' => [],
        ]);

        $this->sendMessage($chatId, "✅ Ma'lumotlaringiz muvaffaqiyatli qabul qilindi.\n\n⏳ Profilingiz tekshiruvda.", $this->getMainKeyboard($user));
        $user->update(['telegram_state' => null]);
    }
}