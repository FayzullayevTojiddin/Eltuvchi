<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Events\OrderChangedSendMessageEvent;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class ClientCompletedOrderController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/client/orders/{order}/complete",
     *     summary="Complete an order by the client",
     *     tags={"Client Orders"},
     *     description="Allows the client to complete a stopped order, transfer deposit to the driver, and notify the driver via event.",
     *     operationId="clientCompleteOrder",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="order",
     *         in="path",
     *         required=true,
     *         description="ID of the order to complete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order successfully completed",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="order_id", type="integer", example=1),
     *                 @OA\Property(property="status", type="string", example="completed"),
     *                 @OA\Property(property="driver_credited", type="number", format="decimal", example=20000)
     *             ),
     *             @OA\Property(property="message", type="string", example="Order successfully completed.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Client not allowed to complete this order",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="data", type="array", @OA\Items(), example={}),
     *             @OA\Property(property="error", type="string", example="You are not allowed to complete this order.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Order is not stopped and cannot be completed",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="data", type="array", @OA\Items(), example={}),
     *             @OA\Property(property="error", type="string", example="Only stopped orders can be completed.")
     *         )
     *     )
     * )
     */

    public function complete_order(Order $order)
    {
        $client = Auth::user()->client;

        if ($order->client_id !== $client->id) {
            return $this->error(
                data: [],
                error_message: 'You are not allowed to complete this order.',
                status: 403
            );
        }

        if ($order->status !== OrderStatus::Stopped) {
            return $this->error(
                data: [],
                error_message: 'Only stopped orders can be completed.',
                status: 400
            );
        }

        $driver = $order->driver;
        $deposit = $order->client_deposit ?? 0;

        if ($driver && $deposit > 0) {
            $driver->addBalance($deposit, "Deposit from order #{$order->id} completed by client");
        }

        $order->status = OrderStatus::Completed;
        $order->save();

        $order->logStatusChange(
            status: OrderStatus::Completed->value,
            user: $client,
            description: 'Order completed by client'
        );

        if ($driver) {
            event(new OrderChangedSendMessageEvent(
                user: $driver->user,
                message: "Order #{$order->id} has been completed by the client. Your deposit has been received."
            ));
        }

        return $this->success(
            data: [
                'order_id' => $order->id,
                'status' => 'completed',
                'driver_credited' => $deposit,
            ],
            message: 'Order successfully completed.'
        );
    }
}