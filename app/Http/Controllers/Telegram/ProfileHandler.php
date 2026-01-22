<?php

namespace App\Http\Controllers\Telegram;

class ProfileHandler extends BaseTelegramController
{
    public function handler($chatId, $user)
    {
        if (!$user) {
            $this->sendMessage($chatId, "❌ Siz ro'yxatdan o'tmagansiz!\n\n👉 Iltimos /start buyrug'ini yuboring.");
            return;
        }

        $connected = $user->connected;
        
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
}
