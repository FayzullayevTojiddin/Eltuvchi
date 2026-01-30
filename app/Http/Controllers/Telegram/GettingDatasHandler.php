<?php

namespace App\Http\Controllers\Telegram;

use App\Models\User;

class GettingDatasHandler extends BaseTelegramController
{
    public function handler($text, $chatId)
    {
        $user = User::where('telegram_id', $chatId)->first();

        $pattern = '/
            Ism:\s*(.+)\n
            Telefon:\s*(\+998\d{9})\n
            Litsenziya:\s*([A-Z]{2})\s*(\d+)\n
            Mashina:\s*([A-Z0-9]+)\n
            Model:\s*(.+)\n
            Tajriba:\s*(\d+)
        /x';

        if (!preg_match($pattern, trim($text), $matches)) {
            $this->sendMessage($chatId, "❌ Ma’lumotlar noto‘g‘ri formatda kiritildi.\n\nIltimos, barcha ma’lumotlarni to‘liq va to‘g‘ri kiriting.");
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

        $this->sendMessage($chatId, "✅ Ma’lumotlaringiz muvaffaqiyatli qabul qilindi.\n\n⏳ Profilingiz tekshiruvda.\nStatus: *NEW*", $this->getMainKeyboard($user));
        $user->update([ 'telegram_state' => null ]);
    }
}