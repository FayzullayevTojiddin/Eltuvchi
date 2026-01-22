<?php

namespace App\Events;

use App\Models\Order;
use App\Traits\TelegramBotTrait;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCreated
{
    use Dispatchable, SerializesModels, TelegramBotTrait;

    public function __construct(public Order $order)
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
        
        return "🆕 <b>Yangi buyurtma yaratildi</b>\n\n" .
               "📋 Buyurtma #: {$this->order->id}\n" .
               "🛣 Yo'nalish: {$route->from} → {$route->to}\n" .
               "👥 Yo'lovchilar soni: {$this->order->passengers}\n" .
               "📅 Sana: {$this->order->date->format('d.m.Y')}\n" .
               "🕐 Vaqt: {$this->order->time}\n" .
               "📱 Telefon: {$this->order->phone}\n" .
               ($this->order->note ? "📝 Izoh: {$this->order->note}\n" : "") .
               "\n✅ Buyurtma muvaffaqiyatli yaratildi";
    }

    protected function formatDriverMessage(): string
    {
        $route = $this->order->route;
        
        return "🚗 <b>Sizga yangi buyurtma tayinlandi</b>\n\n" .
               "📋 Buyurtma #: {$this->order->id}\n" .
               "🛣 Yo'nalish: {$route->from} → {$route->to}\n" .
               "👥 Yo'lovchilar: {$this->order->passengers}\n" .
               "📅 Sana: {$this->order->date->format('d.m.Y')}\n" .
               "🕐 Vaqt: {$this->order->time}\n" .
               "📱 Mijoz telefoni: {$this->order->phone}\n" .
               ($this->order->optional_phone ? "📱 Qo'shimcha telefon: {$this->order->optional_phone}\n" : "") .
               ($this->order->note ? "📝 Izoh: {$this->order->note}\n" : "");
    }
}