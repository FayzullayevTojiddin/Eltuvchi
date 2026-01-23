<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Telegram\Bot\Api;

class SendOrderCreatedTelegram implements ShouldQueue
{
    public function handle(OrderCreated $event): void
    {
        $order = $event->order->load('client.user', 'route');

        $telegram = new Api();

        $clientTelegramId = $order->client?->user?->telegram_id;
        if ($clientTelegramId) {
            $telegram->sendMessage([
                'chat_id' => $clientTelegramId,
                'text' => $this->clientText($order),
                'parse_mode' => 'HTML',
            ]);
        }

        $driverTelegramId = $order->driver?->user?->telegram_id;
        if ($driverTelegramId) {
            $telegram->sendMessage([
                'chat_id' => $driverTelegramId,
                'text' => $this->driverText($order),
                'parse_mode' => 'HTML',
            ]);
        }
    }

    private function clientText($order): string
    {
        return
            "🆕 <b>Buyurtma yaratildi</b>\n\n";
            // "📋 Buyurtma ID: #{$order->id}\n" .
            // "🛣 Yo'nalish: {$order->route->name}\n" .
            // "👥 Yo'lovchilar: {$order->passengers} ta\n" .
            // "📅 Sana: {$order->date->format('d.m.Y')}\n" .
            // "🕐 Vaqt: " . date('H:i', strtotime($order->time)) . "\n" .
            // "📱 Telefon: {$order->phone}\n" .
            // ($order->note ? "📝 Izoh: {$order->note}\n" : "") .
            // "\n✅ Buyurtmangiz muvaffaqiyatli qabul qilindi";
    }

    private function driverText($order): string
    {
        return
            "🚗 <b>Sizga yangi buyurtma tayinlandi</b>\n\n" ;
            // "📋 Buyurtma ID: #{$order->id}\n" .
            // "🛣 Yo'nalish: {$order->route->name}\n" .
            // "👥 Yo'lovchilar: {$order->passengers} ta\n" .
            // "📅 Sana: {$order->date->format('d.m.Y')}\n" .
            // "🕐 Vaqt: " . date('H:i', strtotime($order->time)) . "\n" .
            // "📱 Mijoz telefoni: {$order->phone}\n" .
            // ($order->optional_phone ? "📱 Qo'shimcha: {$order->optional_phone}\n" : "") .
            // ($order->note ? "📝 Izoh: {$order->note}" : "");
    }
}