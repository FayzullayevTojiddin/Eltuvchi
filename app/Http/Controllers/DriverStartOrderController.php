<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class DriverStartOrderController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/driver/orders/{order}/start",
     *     operationId="DriverStartOrder",
     *     tags={"Driver Orders"},
     *     summary="Start an order by driver",
     *     description="Allows a driver to start their assigned order. Order must have status 'Accepted'.",
     *     @OA\Parameter(
     *         name="order",
     *         in="path",
     *         description="ID of the order to start",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order successfully started",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="order_id", type="integer", example=123),
     *                 @OA\Property(property="status", type="string", example="started")
     *             ),
     *             @OA\Property(property="message", type="string", example="Order successfully started.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Order cannot be started because it's not accepted",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="error", type="string", example="Only accepted orders can be started.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Driver not allowed to start this order",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="error", type="string", example="You are not allowed to start this order.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="error", type="string", example="Order not found.")
     *         )
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function start_order(Order $order)
    {
        $driver = Auth::user()->driver;
        if ($order->driver_id !== $driver->id) {
            return $this->error(data: [], error_message: 'You are not allowed to start this order.', status: 403);
        }

        if ($order->status !== OrderStatus::Accepted) {
            return $this->error(error_message: 'Only accepted orders can be started.', status: 400);
        }

        $order->status = OrderStatus::Started->value;
        $order->save();

        $order->logStatusChange(status: OrderStatus::Started->value, user: $driver, description: 'Order started by driver');

        return $this->success(data: [
            'order_id' => $order->id,
            'status' => 'started',
        ], message: 'Order successfully started.');
    }
}