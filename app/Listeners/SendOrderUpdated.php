<?php

namespace App\Listeners;

use App\Events\OrderUpdated;
use App\Traits\TelegramBotTrait;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\Log;

class SendOrderUpdated
{
    use TelegramBotTrait;

    public function handle(OrderUpdated $event): void
    {
        try {
            if (! $event->order->wasChanged('status')) {
                return;
            }

            $order = $event->order->fresh([
                'client.user',
                'driver.user',
                'route',
            ]);

            $message = $this->buildStatusMessage($order);

            if (! $message) {
                return;
            }

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

    private function buildStatusMessage($order): ?string
    {
        return match ($order->status) {

            OrderStatus::Accepted->value =>
                "🚖 <b>Taksi biriktirildi</b>\n\n" .
                $this->baseInfo($order),

            OrderStatus::Started->value =>
                "▶️ <b>Sizning safaringiz boshlandi</b>\n\n" .
                $this->baseInfo($order),

            OrderStatus::Completed->value =>
                "✅ <b>Safar yakunlandi</b>\n\n" .
                $this->baseInfo($order),

            OrderStatus::Cancelled->value =>
                "❌ <b>Buyurtma bekor qilindi</b>\n\n" .
                $this->baseInfo($order),

            default => null,
        };
    }

    private function baseInfo($order): string
    {
        return
            "📋 <b>ID:</b> #{$order->id}\n" .
            "🛣 <b>Yo'nalish:</b> {$order->route?->name}\n" .
            "👥 <b>Yo'lovchilar:</b> {$order->passengers} ta\n" .
            "📅 <b>Sana:</b> {$order->date->format('d.m.Y')}\n" .
            "🕐 <b>Vaqt:</b> " . date('H:i', strtotime($order->time)) . "\n" .
            ($order->driver
                ? "🚖 <b>Haydovchi:</b> {$order->driver->user?->name}\n"
                : ""
            ) .
            ($order->note ? "📝 <b>Izoh:</b> {$order->note}\n" : "");
    }
}