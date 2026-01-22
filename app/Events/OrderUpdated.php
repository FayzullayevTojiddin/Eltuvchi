<?php

namespace App\Events;

use App\Models\Order;
use App\Traits\TelegramBotTrait;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderUpdated
{
    use Dispatchable, SerializesModels, TelegramBotTrait;

    public function __construct(public Order $order, public string $description)
    {
        $this->sendNotifications();
    }

    protected function sendNotifications(): void
    {
        if ($this->order->client && $this->order->client->telegram_id) {
            $message = $this->formatClientMessage();
            $this->sendTelegramMessage($this->order->client->telegram_id, $message);
        }

        if ($this->order->driver && $this->order->driver->telegram_id) {
            $message = $this->formatDriverMessage();
            $this->sendTelegramMessage($this->order->driver->telegram_id, $message);
        }
    }

    protected function formatClientMessage(): string
    {
        $route = $this->order->route;
        
        return "🔄 <b>Buyurtma yangilandi</b>\n\n" .
               "📋 Buyurtma #: {$this->order->id}\n" .
               "🛣 Yo'nalish: {$route->from} → {$route->to}\n" .
               "📊 Status: {$this->order->status}\n" .
               "📝 Tavsif: {$this->description}\n" .
               "📅 Sana: {$this->order->date->format('d.m.Y')}\n" .
               "🕐 Vaqt: {$this->order->time}";
    }

    protected function formatDriverMessage(): string
    {
        $route = $this->order->route;
        
        return "🔄 <b>Buyurtma yangilandi</b>\n\n" .
               "📋 Buyurtma #: {$this->order->id}\n" .
               "🛣 Yo'nalish: {$route->from} → {$route->to}\n" .
               "📊 Status: {$this->order->status}\n" .
               "📝 Tavsif: {$this->description}\n" .
               "📅 Sana: {$this->order->date->format('d.m.Y')}\n" .
               "🕐 Vaqt: {$this->order->time}\n" .
               "📱 Mijoz telefoni: {$this->order->phone}";
    }
}