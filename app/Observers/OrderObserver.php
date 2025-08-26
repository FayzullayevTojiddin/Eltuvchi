<?php

namespace App\Observers;

use App\Events\OrderCreated;
use App\Events\OrderUpdated;
use App\Models\Order;
use App\Traits\TelegramBotTrait;

class OrderObserver
{
    use TelegramBotTrait;

    public function created(Order $order): void
    {
        $order->load('client', 'route');
        $clientId = $order->client->id;
        $fromTaxoparkId = $order->route->taxopark_from_id;
        $toTaxoparkId = $order->route->taxopark_to_id;
        broadcast(new OrderCreated($order, "client.{$clientId}.orders"));
        broadcast(new OrderCreated($order, "taxopark.{$fromTaxoparkId}.orders"));
        broadcast(new OrderCreated($order, "taxopark.{$toTaxoparkId}.orders"));
    }

    public function updated(Order $order): void
    {
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
    }
}