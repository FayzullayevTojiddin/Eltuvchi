<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Models\BalanceHistory;

class OrderCreatedListener
{
    public function handle(OrderCreated $event): void
    {
        $order = $event->order;
        $client = $order->client;
        BalanceHistory::create([
            'user_id' => $client->id,
            'user_type' => $client->getMorphClass(),
            'amount' => -$order->client_deposit,
            'description' => 'Deposit taken for order #' . $order->id,
            'meta' => [
                'order_id' => $order->id,
                'route_id' => $order->route_id,
                'passengers' => $order->passengers,
            ],
        ]);
    }
}