<?php
namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class OrderCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order->load(['client', 'route']);
    }

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('orders');
    }

    public function broadcastAs(): string
    {
        return 'order.created';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->order->id,
            'status' => $this->order->status,
            'client' => [
                'id' => $this->order->client_id,
                'name' => optional($this->order->client)->name,
            ],
            'route' => [
                'id' => $this->order->route_id,
            ],
            'passengers' => $this->order->passengers,
            'price_summ' => $this->order->price_summ,
            'client_deposit' => $this->order->client_deposit,
            'created_at' => $this->order->created_at->toDateTimeString(),
        ];
    }
}