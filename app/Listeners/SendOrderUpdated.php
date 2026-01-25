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
        Log::info('=== SendOrderUpdated HANDLE STARTED ===', [
            'order_id' => $event->order->id ?? 'NO_ID',
        ]);

        try {
            Log::info('Step 1: Checking wasChanged');
            
            // VAQTINCHA wasChanged ni o'chirish
            // if (! $event->order->wasChanged('status')) {
            //     Log::info('Status NOT changed - SKIPPING');
            //     return;
            // }

            Log::info('Step 2: Loading relations');

            $order = $event->order->load([
                'client.user',
                'driver.user',
                'route',
            ]);

            Log::info('Step 3: Relations loaded', [
                'has_client' => isset($order->client),
                'has_driver' => isset($order->driver),
            ]);

            Log::info('Step 4: Building message');
            
            $message = $this->buildStatusMessage($order);

            Log::info('Step 5: Message built', [
                'message_is_null' => is_null($message),
                'message' => $message,
            ]);

            if (! $message) {
                Log::warning('No message - RETURNING');
                return;
            }

            Log::info('Step 6: Getting telegram IDs');

            $clientTelegramId = $order->client?->user?->telegram_id;
            $driverTelegramId = $order->driver?->user?->telegram_id;

            Log::info('Step 7: Telegram IDs', [
                'client_id' => $clientTelegramId,
                'driver_id' => $driverTelegramId,
            ]);

            if ($clientTelegramId) {
                Log::info('Step 8: Sending to client');
                $this->sendTelegramMessage($clientTelegramId, $message);
                Log::info('Step 9: Client message sent');
            }

            if ($driverTelegramId) {
                Log::info('Step 10: Sending to driver');
                $this->sendTelegramMessage($driverTelegramId, $message);
                Log::info('Step 11: Driver message sent');
            }

            Log::info('=== HANDLE COMPLETED ===');

        } catch (\Throwable $e) {
            Log::error('!!! EXCEPTION CAUGHT !!!', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
        }
    }

    private function buildStatusMessage($order): ?string
    {
        Log::info('buildStatusMessage called', [
            'status' => $order->status,
        ]);

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