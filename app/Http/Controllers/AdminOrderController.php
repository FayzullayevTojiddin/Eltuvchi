<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Resources\AdminOrderResource;
use Illuminate\Http\Request;
use App\Enums\OrderStatus;

class AdminOrderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/admin/orders",
     *     summary="Get all orders",
     *     tags={"Admin Orders"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         description="Filter orders by status",
     *         @OA\Schema(type="string", enum={"pending","accepted","completed","cancelled"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of orders",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/AdminOrder"))
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Order::with(['client', 'driver', 'route', 'histories', 'review']);

        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        $orders = $query->paginate(20);

        return AdminOrderResource::collection($orders);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/orders/{id}",
     *     summary="Get single order by ID",
     *     tags={"Admin Orders"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Order ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order details",
     *         @OA\JsonContent(ref="#/components/schemas/AdminOrder")
     *     )
     * )
     */
    public function show(Order $order)
    {
        $order->load(['client', 'driver', 'route', 'histories', 'review']);

        return new AdminOrderResource($order);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/orders/{id}",
     *     summary="Update order by ID",
     *     tags={"Admin Orders"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Order ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="completed"),
     *             @OA\Property(property="note", type="string", example="Admin updated order"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated order",
     *         @OA\JsonContent(ref="#/components/schemas/AdminOrder")
     *     )
     * )
     */
    public function update(Request $request, Order $order)
    {
        $oldStatus = $order->status;

        $order->update($request->all());

        if ($order->wasChanged()) {
            $order->logStatusChange(
                $order->status->value,
                $request->user(),
                "Order updated by admin"
            );
        }

        return new AdminOrderResource($order->fresh(['client', 'driver', 'route', 'histories', 'review']));
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/orders/{id}",
     *     summary="Cancel order by ID",
     *     tags={"Admin Orders"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Order ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order cancelled successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Order cancelled successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/AdminOrder")
     *         )
     *     )
     * )
     */
    public function destroy(Request $request, Order $order)
    {
        if ($order->status !== OrderStatus::Cancelled->value) {
            $order->update(['status' => OrderStatus::Cancelled->value]);

            $order->logStatusChange(
                OrderStatus::Cancelled->value,
                $request->user(),
                "Order cancelled by admin"
            );
        }

        return response()->json([
            'message' => 'Order cancelled successfully',
            'data' => new AdminOrderResource($order->fresh(['client', 'driver', 'route', 'histories', 'review']))
        ]);
    }
}