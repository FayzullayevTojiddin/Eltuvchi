<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Traits\TelegramBotTrait;

class SendOrderCreated
{
    use TelegramBotTrait;

    public function handle(OrderCreated $event): void
    {
        try {
            $order = $event->order->load([
                'client.user',
                'route',
            ]);

            $telegramId = $order->client?->user?->telegram_id;

            if (! $telegramId) {
                return;
            }

            $message = $this->buildMessage($order);

            $this->sendTelegramMessage($telegramId, $message);

        } catch (\Throwable $e) {
            //
        }
    }

    private function buildMessage($order): string
    {
        return
            "🆕 <b>Buyurtma yaratildi</b>\n\n" .
            "📋 <b>ID:</b> #{$order->id}\n" .
            "🛣 <b>Yo'nalish:</b> {$order->route?->name}\n" .
            "👥 <b>Yo'lovchilar:</b> {$order->passengers} ta\n" .
            "📅 <b>Sana:</b> {$order->date->format('d.m.Y')}\n" .
            "🕐 <b>Vaqt:</b> " . date('H:i', strtotime($order->time)) . "\n" .
            "📱 <b>Telefon:</b> {$order->phone}\n" .
            ($order->note ? "📝 <b>Izoh:</b> {$order->note}\n" : "") .
            "\n✅ Buyurtmangiz qabul qilindi.";
    }
}