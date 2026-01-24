<?php

namespace App\Listeners;

use App\Events\OrderUpdated;
use App\Traits\TelegramBotTrait;
use Illuminate\Support\Facades\Log;

class SendOrderUpdated
{
    use TelegramBotTrait;

    public function handle(OrderUpdated $event): void
    {
        try {
            $order = $event->order->fresh([
                'client.user',
                'driver.user',
                'route',
            ]);

            $message = $this->buildMessage($order);

            $clientTelegramId = $order->client?->user?->telegram_id;
            if ($clientTelegramId) {
                $this->sendTelegramMessage($clientTelegramId, $message);
            }

            $driverTelegramId = $order->driver?->user?->telegram_id;
            if ($driverTelegramId) {
                $this->sendTelegramMessage($driverTelegramId, $message);
            }

        } catch (\Throwable $e) {
            Log::error('SendOrderUpdated listener error', [
                'order_id' => $event->order->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function buildMessage($order): string
    {
        return
            "✏️ <b>Buyurtma yangilandi</b>\n\n" .
            "📋 <b>ID:</b> #{$order->id}\n" .
            "🛣 <b>Yo'nalish:</b> {$order->route?->name}\n" .
            "👥 <b>Yo'lovchilar:</b> {$order->passengers} ta\n" .
            "📅 <b>Sana:</b> {$order->date->format('d.m.Y')}\n" .
            "🕐 <b>Vaqt:</b> " . date('H:i', strtotime($order->time)) . "\n" .
            "📱 <b>Telefon:</b> {$order->phone}\n" .
            ($order->driver
                ? "🚖 <b>Haydovchi:</b> {$order->driver->user?->name}\n"
                : "🚖 <b>Haydovchi:</b> Biriktirilmagan\n"
            ) .
            ($order->note ? "📝 <b>Izoh:</b> {$order->note}\n" : "") .
            "\nℹ️ Buyurtma ma’lumotlari yangilandi.";
    }
}