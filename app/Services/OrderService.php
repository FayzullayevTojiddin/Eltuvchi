<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\Client;
use App\Models\Driver;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;

class OrderService
{
    public function create(array $data, Client $client): ?Order
    {
        $deposit = config('order.deposit_per_passenger') * $data['passengers'];

        if ($client->balance < $deposit) {
            return null;
        }

        $client->decrement('balance', $deposit);

        return $client->orders()->create([
            ...$data,
            'status' => OrderStatus::Pending,
            'client_deposit' => $deposit,
        ]);
    }

    public function index(Request $request)
    {
        $client = $request->user()->client;
        return $client->orders()->latest()->get();
    }

    public function cancel(int $orderId, Client $client): array
    {
        $order = $client->orders()->find($orderId);
        if (!$order) {
            return ['status' => 'error', 'code' => 404, 'message' => 'Order not found.'];
        }

        $status = $order->status;
        if ($status === OrderStatus::Cancelled) {
            return ['status' => 'error', 'code' => 400, 'message' => 'This order has already been cancelled.'];
        }

        if (in_array($status, [
            OrderStatus::Started,
            OrderStatus::Arrived,
            OrderStatus::Completed
        ])) {
            return ['status' => 'error', 'code' => 403, 'message' => 'This order has already been started and cannot be cancelled.'];
        }

        $deposit = $order->client_deposit;
        $description = '';

        if ($status === OrderStatus::Pending) {
            $client->increment('balance', $deposit);
            $description = 'Order was cancelled by client before it was accepted.';
        } elseif ($status === OrderStatus::Accepted) {
            if (!$order->driver) {
                return ['status' => 'error', 'code' => 500, 'message' => 'Driver is missing for accepted order.'];
            }

            $driver = $order->driver;
            $driver->increment('balance', $deposit);
            $description = 'Order was cancelled by client after it was accepted. Deposit sent to driver.';
        }

        $order->update(['status' => OrderStatus::Cancelled]);

        OrderHistory::create([
            'order_id' => $order->id,
            'status' => OrderStatus::Cancelled,
            'changed_by_id' => $client->id,
            'changed_by_type' => $client->getMorphClass(),
            'description' => $description,
        ]);

        return ['status' => 'success', 'message' => 'Order cancelled successfully.'];
    }

    public function show(int $id, Client $client): ?Order
    {
        return $client->orders()->with([
            'route.fromTaxopark.region',
            'route.toTaxopark.region',
            'driver',
            'histories'
        ])->find($id);
    }

    public function complete(int $id): array
    {
        $order = Order::with('driver')->find($id);
        if (!$order) {
            return ['status' => 'error', 'code' => 404, 'message' => 'Order not found'];
        }

        if ($order->status !== OrderStatus::Started) {
            return ['status' => 'error', 'code' => 403, 'message' => 'Only started orders can be completed'];
        }

        $order->update(['status' => OrderStatus::Completed]);
        $order->driver->increment('balance', $order->client_deposit);

        return ['status' => 'success', 'order' => $order];
    }
}