<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Throwable;

class SendOrderCreatedTelegram implements ShouldQueue
{
    public int $tries = 1;
    public function handle(OrderCreated $event): void
    {
        try {
            $order = $event->order->fresh(['client.user', 'route']);

            $clientTelegramId = $order->client?->user?->telegram_id;
            if (! $clientTelegramId) {
                return;
            }

            $telegram = new Api();

            $telegram->sendMessage([
                'chat_id' => $clientTelegramId,
                'text' => $this->clientText($order),
                'parse_mode' => 'HTML',
            ]);
        } catch (Throwable $e) {
            Log::error('Telegram order created failed', [
                'order_id' => $event->order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function clientText($order): string
    {
        return
            "🆕 <b>Buyurtma yaratildi</b>\n\n".
            "📋 Buyurtma ID: #{$order->id}\n" .
            "🛣 Yo'nalish: {$order->route->name}\n" .
            "👥 Yo'lovchilar: {$order->passengers} ta\n" .
            "📅 Sana: {$order->date->format('d.m.Y')}\n" .
            "🕐 Vaqt: " . date('H:i', strtotime($order->time)) . "\n" .
            "📱 Telefon: {$order->phone}\n" .
            ($order->note ? "📝 Izoh: {$order->note}\n" : "") .
            "\n✅ Buyurtmangiz muvaffaqiyatli qabul qilindi";
    }
}