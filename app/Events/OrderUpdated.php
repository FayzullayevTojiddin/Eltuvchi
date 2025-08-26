<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class OrderUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public array $order;
    public string $channel;

    public function __construct(Order $order, string $channel)
    {
        $this->order = $order->toArray();
        $this->channel = $channel;
    }

    public function broadcastOn()
    {
        return new PrivateChannel($this->channel);
    }
}