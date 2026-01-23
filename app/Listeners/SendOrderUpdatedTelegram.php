<?php

namespace App\Listeners;

use App\Events\OrderUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Telegram\Bot\Api;

class SendOrderUpdatedTelegram implements ShouldQueue
{
    public function handle(OrderUpdated $event): void
    {
        // $order = $event->order->load('client.user', 'driver.user', 'route');
        // $description = $event->description;

        // $telegram = new Api();

        // $clientTelegramId = $order->client?->user?->telegram_id;
        // if ($clientTelegramId) {
        //     $telegram->sendMessage([
        //         'chat_id' => $clientTelegramId,
        //         'text' => $this->clientText($order, $description),
        //         'parse_mode' => 'HTML',
        //     ]);
        // }

        // $driverTelegramId = $order->driver?->user?->telegram_id;
        // if ($driverTelegramId) {
        //     $telegram->sendMessage([
        //         'chat_id' => $driverTelegramId,
        //         'text' => $this->driverText($order, $description),
        //         'parse_mode' => 'HTML',
        //     ]);
        // }
    }

    private function clientText($order, $description): string
    {
        return
            "🔄 <b>Buyurtma yangilandi</b>\n\n" .
            "📋 Buyurtma ID: #{$order->id}\n" .
            "🛣 Yo'nalish: {$order->route->name}\n" .
            "📊 Status: {$order->status->label()}\n" .
            "📝 Tavsif: {$description}\n" .
            "📅 Sana: {$order->date->format('d.m.Y')}\n" .
            "🕐 Vaqt: " . date('H:i', strtotime($order->time));
    }

    private function driverText($order, $description): string
    {
        return
            "🔄 <b>Buyurtma yangilandi</b>\n\n" .
            "📋 Buyurtma ID: #{$order->id}\n" .
            "🛣 Yo'nalish: {$order->route->name}\n" .
            "📊 Status: {$order->status->label()}\n" .
            "📝 Tavsif: {$description}\n" .
            "📅 Sana: {$order->date->format('d.m.Y')}\n" .
            "🕐 Vaqt: " . date('H:i', strtotime($order->time)) . "\n" .
            "📱 Mijoz: {$order->phone}";
    }
}