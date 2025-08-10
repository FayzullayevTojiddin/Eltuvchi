<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientCancelOrderController extends Controller
{
    public function cancel(Request $request, int $orderId)
    {
        $user = $request->user();

        $order = Order::with(['driver', 'client'])->find($orderId);

        if (!$order) {
            return $this->error([], 404, 'Order not found.');
        }

        if ($order->client_id !== $user->id) {
            return $this->error([], 403, 'Unauthorized.');
        }

        if ($order->status === OrderStatus::Cancelled) {
            return $this->error([], 400, 'Order already cancelled.');
        }

        DB::beginTransaction();
        try {
            $depositPercent = $order->discount_percent; 
            $depositAmount = $order->client_deposit;

            if ($order->status === OrderStatus::Created) {
                $refundAmount = $depositAmount * (1 - $depositPercent / 100);
                $order->client->addBalance($refundAmount, "Refund deposit for cancelled order #{$order->id}");
            } else {
                $order->driver->addBalance($depositAmount, "Deposit from cancelled order #{$order->id}");
            }

            $order->status = OrderStatus::Cancelled;
            $order->save();

            $order->logStatusChange(OrderStatus::Cancelled->value, $user, 'Order cancelled by client.');

            DB::commit();

            return $this->success(new OrderResource($order), 200, 'Order cancelled successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->error([], 500, 'Cancellation failed: ' . $e->getMessage());
        }
    }
}