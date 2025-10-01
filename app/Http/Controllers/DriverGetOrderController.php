<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DriverGetOrderController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/driver/orders/{order}",
     *     summary="Driver accepts an order",
     *     description="Assigns the authenticated driver to the given order if all checks pass.",
     *     tags={"Driver Orders"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="order",
     *         in="path",
     *         required=true,
     *         description="ID of the order to accept",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order successfully accepted",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Order successfully accepted."),
     *             @OA\Property(property="data", ref="#/components/schemas/Order")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Order cannot be accepted due to various business rules",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="error", type="string", example="This order already has a driver assigned."),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="error", type="string", example="Unauthenticated"),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="error", type="string", example="Order not found"),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="error", type="string", example="Internal server error"),
     *             @OA\Property(property="data", type="object", example={})
     *         )
     *     )
     * )
     */
    public function get_order(Order $order): JsonResponse
    {
        $driver = Auth::user()->driver;
        $route = $order->route;
        $driver_payment = $route->fee_per_client * $order->passengers;

        if ($order->driver_id) {
            return $this->error(data: [], status: 400, error_message: 'This order already has a driver assigned.');
        }

        if ($order->route->taxopark_from_id !== $driver->taxopark_id
            && $route->taxopark_to_id !== $driver->taxopark_id) {
            return $this->error(data: [], status: 400, error_message: 'You cannot take this order because it is not in your taxopark route.');
        }

        if ($driver->balance < $driver_payment) {
            return $this->error(data: [], status: 400, error_message: 'Insufficient balance to accept this order.');
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

        return $this->success(data: new OrderResource($order->load('driver')), message: 'Order successfully accepted.', status: 200);
    }
}
