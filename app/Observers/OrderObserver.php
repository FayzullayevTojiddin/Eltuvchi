<?php

namespace App\Observers;

use App\Events\OrderCreated;
use App\Models\Order;
use App\Traits\TelegramBotTrait;

class OrderObserver
{
    use TelegramBotTrait;

    public function created(Order $order): void
    {
        // $user = $order->client->user;
        // $test_id = 6176109572;
        // broadcast(new OrderCreated($order));
        // $this->sendTelegramMessage($test_id, 'Hisobingizdan Order: ' . $order->id . " yatildi !");
    }

    public function updated(Order $order): void
    {
        //
    }

    public function deleted(Order $order): void
    {
        //
    }

    public function restored(Order $order): void
    {
        //
    }

    public function forceDeleted(Order $order): void
    {
        //
    }
}
