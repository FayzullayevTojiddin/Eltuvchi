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
        // BIRINCHI LOG - handle ishga tushganini ko'rish uchun
        Log::info('=== SendOrderUpdated HANDLE STARTED ===', [
            'order_id' => $event->order->id ?? 'NO_ID',
            'order_exists' => isset($event->order),
        ]);

        try {
            // wasChanged() tekshiruvini VAQTINCHA o'chirib qo'yamiz
            // if (! $event->order->wasChanged('status')) {
            //     Log::info('Status was not changed, skipping');
            //     return;
            // }

            Log::info('Before loading relations', [
                'order_id' => $event->order->id,
            ]);

            $order = $event->order->load([
                'client.user',
                'driver.user',
                'route',
            ]);

            Log::info('After loading relations', [
                'order_id' => $order->id,
                'status' => $order->status,
                'has_client' => isset($order->client),
                'has_driver' => isset($order->driver),
                'has_route' => isset($order->route),
                'client_telegram_id' => $order->client?->user?->telegram_id ?? 'NULL',
                'driver_telegram_id' => $order->driver?->user?->telegram_id ?? 'NULL',
            ]);

            $message = $this->buildStatusMessage($order);

            Log::info('Message built', [
                'order_id' => $order->id,
                'message_length' => strlen($message ?? ''),
                'message_preview' => substr($message ?? '', 0, 100),
                'message_is_null' => is_null($message),
            ]);

            if (! $message) {
                Log::warning('No message to send (message is null or empty)', [
                    'order_id' => $order->id,
                    'order_status' => $order->status,
                ]);
                return;
            }

            $clientTelegramId = $order->client?->user?->telegram_id;
            Log::info('Client telegram check', [
                'client_telegram_id' => $clientTelegramId,
                'has_client' => isset($order->client),
                'has_client_user' => isset($order->client->user),
            ]);

            if ($clientTelegramId) {
                Log::info('Attempting to send to CLIENT', [
                    'telegram_id' => $clientTelegramId,
                    'order_id' => $order->id,
                    'message' => $message,
                ]);
                
                $result = $this->sendTelegramMessage($clientTelegramId, $message);
                
                Log::info('Client message sent result', [
                    'result' => $result,
                ]);
            } else {
                Log::warning('Client telegram_id is empty/null', [
                    'order_id' => $order->id,
                ]);
            }

            $driverTelegramId = $order->driver?->user?->telegram_id;
            Log::info('Driver telegram check', [
                'driver_telegram_id' => $driverTelegramId,
                'has_driver' => isset($order->driver),
                'has_driver_user' => isset($order->driver->user),
            ]);

            if ($driverTelegramId) {
                Log::info('Attempting to send to DRIVER', [
                    'telegram_id' => $driverTelegramId,
                    'order_id' => $order->id,
                    'message' => $message,
                ]);
                
                $result = $this->sendTelegramMessage($driverTelegramId, $message);
                
                Log::info('Driver message sent result', [
                    'result' => $result,
                ]);
            } else {
                Log::warning('Driver telegram_id is empty/null', [
                    'order_id' => $order->id,
                ]);
            }

            Log::info('=== SendOrderUpdated HANDLE COMPLETED ===');

        } catch (\Throwable $e) {
            Log::error('SendOrderUpdated listener EXCEPTION', [
                'order_id' => $event->order->id ?? null,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    private function buildStatusMessage($order): ?string
    {
        Log::info('Building status message', [
            'order_status' => $order->status,
            'order_status_type' => gettype($order->status),
        ]);

        $result = match ($order->status) {

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

        Log::info('Status message match result', [
            'result_is_null' => is_null($result),
            'result_preview' => substr($result ?? '', 0, 50),
        ]);

        return $result;
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