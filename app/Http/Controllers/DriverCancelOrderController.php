<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverCancelOrderController extends Controller
{
    public function cancel_order(Order $order)
    {
        $driver = Auth::user()->driver;

        if ($order->driver_id !== $driver->id) {
            return $this->error('You are not allowed to cancel this order.', 403);
        }

        $order->driver_id = null;
        $order->save();

        $amount = $order->route->deposit_client * $order->passengers;
        $order->client->addBalance($amount, "Refund for order #{$order->id} canceled by driver");

        $order->logStatusChange('driver_removed', $driver, 'Driver removed from the order');
        return $this->success([
            'order_id' => $order->id,
            'status' => 'cancelled',
        ], 'Order successfully cancelled.');
    }
}