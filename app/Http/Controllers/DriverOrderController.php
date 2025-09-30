<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientOrderResource;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class DriverOrderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/driver/orders",
     *     summary="Get all orders for driver's taxopark",
     *     description="Returns all orders related to the driver's taxopark",
     *     tags={"Driver Orders"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of orders",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ClientOrder")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function index()
    {
        $driver = Auth::user()->driver;
        $orders = Order::query()
            ->whereHas('route', function ($q) use ($driver) {
                $q->where('taxopark_from_id', $driver->taxopark_id)
                    ->orWhere('taxopark_to_id', $driver->taxopark_id);
            })
            ->where('status', 'created')
            ->with(['route'])
            ->latest()
            ->get();

        return $this->response(ClientOrderResource::collection($orders));
    }

    /**
     * @OA\Get(
     *     path="/api/driver/my_orders",
     *     summary="Get driver's orders",
     *     description="Returns orders assigned to the driver, optionally filtered by status",
     *     tags={"Driver Orders"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter orders by status",
     *         required=false,
     *         @OA\Schema(type="string", example="Completed")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of driver's orders",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ClientOrder")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function my_orders()
    {
        $driver = Auth::user()->driver;
        $query = Order::with('route', 'histories', 'client', 'review')->where('driver_id', $driver->id);
        if ($status = request('status')) {
            $query->where('status', $status);
        }
        $orders = $query->get();
        return $this->response(ClientOrderResource::collection($orders));
    }
}
