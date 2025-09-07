<?php

namespace App\Observers;

use App\Enums\OrderStatus;
use App\Events\OrderCreated;
use App\Events\OrderUpdated;
use App\Models\Order;
use App\Traits\TelegramBotTrait;
use Auth;

class OrderObserver
{
    use TelegramBotTrait;

    public function created(Order $order): void
    {
        $user = Auth::user();
        $order->load('client', 'route');
        $clientId = $order->client->id;
        $fromTaxoparkId = $order->route->taxopark_from_id;
        $toTaxoparkId = $order->route->taxopark_to_id;
        broadcast(new OrderCreated($order, "client.{$clientId}.orders"));
        broadcast(new OrderCreated($order, "taxopark.{$fromTaxoparkId}.orders"));
        broadcast(new OrderCreated($order, "taxopark.{$toTaxoparkId}.orders"));
        $order->histories()->create([
            'status' => OrderStatus::Created,
            'changed_by_id' => $user->id,
            'changed_by_type' => get_class($user),
            'description' => 'Order created',
        ]);
    }

    public function updated(Order $order): void
    {
        $user = Auth::user();
        $order->load('client', 'route', 'review', 'driver', 'histories');
        $clientId = $order->client->id;
        $fromTaxoparkId = $order->route->taxopark_from_id;
        $toTaxoparkId = $order->route->taxopark_to_id;
        $driverId = $order->driver_id;
        broadcast(new OrderUpdated($order, "client.{$clientId}.orders"));
        broadcast(new OrderUpdated($order, "taxopark.{$fromTaxoparkId}.orders"));
        broadcast(new OrderUpdated($order, "taxopark.{$toTaxoparkId}.orders"));

        if ($driverId) {
            broadcast(new OrderUpdated($order, "driver.{$driverId}.orders"));
        }

        $order->histories()->create([
            'status' => $order->status,
            'changed_by_id' => $user->id,
            'changed_by_type' => get_class($user),
            'description' => $order->temp_description ?? 'Buyurtma yangilandi',
        ]);
    }
}