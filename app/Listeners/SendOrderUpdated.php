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
            // MUAMMO 1: wasChanged() faqat save() oldidan ishlaydi
            // Event dispatch qilingandan keyin ishlamas bo'lishi mumkin
            Log::info('OrderUpdated event triggered', [
                'order_id' => $event->order->id,
                'status' => $event->order->status,
                'original_status' => $event->order->getOriginal('status'), // Qo'shimcha log
            ]);

            // MUAMMO 2: fresh() ichida relation yuklanmagan bo'lishi mumkin
            $order = $event->order->load([
                'client.user',
                'driver.user',
                'route',
            ]);

            $message = $this->buildStatusMessage($order);

            Log::info('Message built', [
                'order_id' => $order->id,
                'message' => $message,
            ]);

            if (! $message) {
                Log::warning('No message to send', ['order_id' => $order->id]);
                return;
            }

            $clientTelegramId = $order->client?->user?->telegram_id;
            if ($clientTelegramId) {
                Log::info('Sending to client', [
                    'telegram_id' => $clientTelegramId,
                    'order_id' => $order->id,
                ]);
                $this->sendTelegramMessage($clientTelegramId, $message);
            } else {
                Log::warning('Client telegram_id not found', [
                    'order_id' => $order->id,
                    'client_id' => $order->client?->id,
                ]);
            }

            $driverTelegramId = $order->driver?->user?->telegram_id;
            if ($driverTelegramId) {
                Log::info('Sending to driver', [
                    'telegram_id' => $driverTelegramId,
                    'order_id' => $order->id,
                ]);
                $this->sendTelegramMessage($driverTelegramId, $message);
            } else {
                Log::warning('Driver telegram_id not found', [
                    'order_id' => $order->id,
                    'driver_id' => $order->driver?->id,
                ]);
            }

        } catch (\Throwable $e) {
            Log::error('SendOrderUpdated listener error', [
                'order_id' => $event->order->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(), // To'liq trace
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