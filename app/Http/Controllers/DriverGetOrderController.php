<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Auth;
use Illuminate\Http\JsonResponse;

class DriverGetOrderController extends Controller
{
    public function get_order(Order $order): JsonResponse
    {
        $driver = Auth::user()->driver;
        $route = $order->route;
        $driver_payment = $route->fee_per_client * $order->passengers;

        if ($order->driver_id) {
            return $this->error(
                data: [], 
                status: 400, 
                error_message: 'Bu buyurtma allaqachon boshqa haydovchi tomonidan qabul qilingan.'
            );
        }

        if ($order->route->taxopark_from_id !== $driver->taxopark_id
            && $route->taxopark_to_id !== $driver->taxopark_id) {
            return $this->error(
                data: [], 
                status: 400, 
                error_message: 'Siz bu buyurtmani qabul qila olmaysiz, chunki u sizning taksopark yo\'nalishida emas.'
            );
        }

        if ($driver->balance < $driver_payment) {
            return $this->error(
                data: [], 
                status: 400, 
                error_message: "Balansingiz yetarli emas. Buyurtmani qabul qilish uchun {$driver_payment} so'm kerak."
            );
        }

        $driver->subtractBalance($driver_payment, "Payment for order #{$order->id}");

        $order->update([
            'driver_id' => $driver->id,
            'status' => OrderStatus::Accepted
        ]);

        $order->logStatusChange(
            status: OrderStatus::Accepted->value,
            user: $driver,
            description: "Driver #{$driver->id} accepted the order."
        );

        return $this->success(
            data: new OrderResource($order->load('driver')), 
            message: 'Buyurtma muvaffaqiyatli qabul qilindi.', 
            status: 200
        );
    }
}
