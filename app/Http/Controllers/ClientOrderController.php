<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientOrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientOrderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/client/orders",
     *     summary="Get all orders of the authenticated client",
     *     tags={"Client Orders"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter orders by status",
     *         required=false,
     *         @OA\Schema(type="string", example="created")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of client orders retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Order")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $client = Auth::user()->client;

        $query = Order::where('client_id', $client->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->get();

        return response()->json([
            'success' => true,
            'data' => ClientOrderResource::collection($orders)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/client/orders/{order_id}",
     *     summary="Get a specific order of the authenticated client",
     *     tags={"Client Orders"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="order_id",
     *         in="path",
     *         description="ID of the order to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer", example=111)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Order retrieved successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/Order")
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
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function show($order_id)
    {
        $order = Order::with(['client', 'driver', 'route', 'histories'])->find($order_id);

        if (!$order) {
            return $this->error([], 404, 'Order not found.');
        }

        return $this->success(new ClientOrderResource($order), 200, 'Order retrieved successfully.');
    }
}