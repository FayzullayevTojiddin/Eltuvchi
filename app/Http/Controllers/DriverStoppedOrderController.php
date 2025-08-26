<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Events\OrderChangedSendMessageEvent;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class DriverStoppedOrderController extends Controller
{
    public function stop_order(Order $order)
    {
        $user = Auth::user();
        $driver = $user->driver;

        if ($order->driver_id !== $driver->id) {
            return $this->error(data: [], error_message: 'You are not allowed to stop this order.', status: 403);
        }

        if ($order->status !== OrderStatus::Started) {
            return $this->error(data: [], error_message: 'Only started orders can be stopped.', status: 400);
        }

        $order->status = OrderStatus::Stopped;
        $order->save();

        // event(new OrderChangedSendMessageEvent(
        //     user: $user, 
        //     message: "Complete your order $order->id"
        // ));

        $order->logStatusChange(OrderStatus::Stopped->value, $driver, 'Order stopped by driver');

        return $this->success(data: [
            'order_id' => $order->id,
            'status' => 'stopped',
        ], message: 'Order successfully stopped.');
    }
}