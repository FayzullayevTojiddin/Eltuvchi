<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientCancelOrderController extends Controller
{
    /**
     * @OA\Delete(
     *     path="/api/client/orders/{orderId}",
     *     summary="Cancel an order by the authenticated client",
     *     tags={"Client Orders"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="orderId",
     *         in="path",
     *         description="ID of the order to cancel",
     *         required=true,
     *         @OA\Schema(type="integer", example=111)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order cancelled successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Order cancelled successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/Order")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Order already cancelled",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Order already cancelled.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Order not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Cancellation failed: Internal server error")
     *         )
     *     )
     * )
     */
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