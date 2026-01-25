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
        Log::info('=== START ===', ['order_id' => $event->order->id ?? 'NO_ID']);

        Log::info('Before try block');
        
        try {
            Log::info('Inside try block - Line 1');
            
            $order = $event->order;
            
            Log::info('Got order', ['id' => $order->id]);
            
            $order->load(['client.user', 'driver.user', 'route']);
            
            Log::info('Loaded relations');
            
            $message = $this->buildStatusMessage($order);
            
            Log::info('Built message', ['msg' => $message]);
            
            if (!$message) {
                Log::info('No message, returning');
                return;
            }
            
            $clientId = $order->client?->user?->telegram_id;
            $driverId = $order->driver?->user?->telegram_id;
            
            Log::info('IDs', ['client' => $clientId, 'driver' => $driverId]);
            
            if ($clientId) {
                Log::info('Sending to client...');
                $this->sendTelegramMessage($clientId, $message);
                Log::info('Sent to client');
            }
            
            if ($driverId) {
                Log::info('Sending to driver...');
                $this->sendTelegramMessage($driverId, $message);
                Log::info('Sent to driver');
            }
            
            Log::info('=== END ===');
            
        } catch (\Throwable $e) {
            Log::error('EXCEPTION', [
                'msg' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
        
        Log::info('After try-catch block');
    }

    private function buildStatusMessage($order): ?string
    {
        return match ($order->status) {
            OrderStatus::Accepted->value => "🚖 <b>Taksi biriktirildi</b>\n\n" . $this->baseInfo($order),
            OrderStatus::Started->value => "▶️ <b>Sizning safaringiz boshlandi</b>\n\n" . $this->baseInfo($order),
            OrderStatus::Completed->value => "✅ <b>Safar yakunlandi</b>\n\n" . $this->baseInfo($order),
            OrderStatus::Cancelled->value => "❌ <b>Buyurtma bekor qilindi</b>\n\n" . $this->baseInfo($order),
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
            ($order->driver ? "🚖 <b>Haydovchi:</b> {$order->driver->user?->name}\n" : "") .
            ($order->note ? "📝 <b>Izoh:</b> {$order->note}\n" : "");
    }
}