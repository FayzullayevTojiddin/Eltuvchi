<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Telegram\Bot\Api;

class SendOrderCreatedTelegram implements ShouldQueue
{
    public function handle(OrderCreated $event): void
    {
        $order = $event->order->load(
            'client.user',
            'driver.user'
        );

        $telegram = new Api();

        $clientTelegramId = $order->client?->user?->telegram_id;
        if ($clientTelegramId) {
            $telegram->sendMessage([
                'chat_id' => $clientTelegramId,
                'text' => $this->clientText($order),
            ]);
        }

        $driverTelegramId = $order->driver?->user?->telegram_id;
        if ($driverTelegramId) {
            $telegram->sendMessage([
                'chat_id' => $driverTelegramId,
                'text' => $this->driverText($order),
            ]);
        }
    }

    private function clientText($order): string
    {
        return
            "🆕 Buyurtma yaratildi\n" .
            "ID: {$order->id}\n" .
            "Yo‘nalish: {$order->route->name}\n" .
            "Status: {$order->status->value}";
    }

    private function driverText($order): string
    {
        return
            "🚕 Sizga yangi buyurtma\n" .
            "ID: {$order->id}\n" .
            "Yo‘nalish: {$order->route->name}";
    }
}