<?php

namespace App\Http\Controllers\Telegram;

class BackToMainHandler extends BaseTelegramController
{
    public function handler($chatId, $user)
    {
        if ($user) {
            $user->update([
                'telegram_state' => null
            ]);
        }

        $this->sendMessage(
            $chatId,
            "🏠 Bosh menyu\n\nKerakli bo‘limni tanlang 👇",
            $this->getMainKeyboard($user)
        );
    }
}