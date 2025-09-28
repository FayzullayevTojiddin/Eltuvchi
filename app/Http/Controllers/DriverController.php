<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Resources\DriverDashboardResource;
use App\Http\Resources\OrderResource;
use App\Models\BalanceHistory;
use App\Models\Order;
use App\Models\OrderReview;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DriverController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/driver/dashboard",
     *     summary="Driver dashboard stats",
     *     description="Returns total income, completed orders count, average rating, and recent orders for authenticated driver",
     *     tags={"Driver"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Driver dashboard data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="total_income", type="integer", example=1500000),
     *             @OA\Property(property="completed_orders_count", type="integer", example=25),
     *             @OA\Property(property="average_rating", type="number", format="float", example=4.75, nullable=true),
     *             @OA\Property(
     *                 property="recent_orders",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Order")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function dashboard()
    {
        $driver = Auth::user()->driver;

        $totalIncome = BalanceHistory::where('balanceable_type', get_class($driver))
            ->where('balanceable_id', $driver->id)
            ->where('amount', '>', 0)
            ->sum('amount');

        $completedOrdersCount = Order::where('driver_id', $driver->id)
            ->where('status', OrderStatus::Completed->value)
            ->count();

        $averageRating = OrderReview::whereHas('order', function ($q) use ($driver) {
                $q->where('driver_id', $driver->id)
                ->where('status', OrderStatus::Completed->value);
            })
            ->avg('score');
        $averageRating = $averageRating ? round($averageRating, 2) : null;

        $recentOrders = Order::where('driver_id', $driver->id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $data = [
            'total_income'           => (int) $totalIncome,
            'completed_orders_count' => $completedOrdersCount,
            'average_rating'         => $averageRating,
            'recent_orders'          => OrderResource::collection($recentOrders),
        ];

        return $this->response(new DriverDashboardResource($data));
    }
}
