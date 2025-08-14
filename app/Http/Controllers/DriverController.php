<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Resources\DriverDashboardResource;
use App\Models\BalanceHistory;
use App\Models\Order;
use App\Models\OrderReview;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DriverController extends Controller
{
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
            'recent_orders'          => $recentOrders,
        ];

        return $this->response(new DriverDashboardResource($data));
    }
}