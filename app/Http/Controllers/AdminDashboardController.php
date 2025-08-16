<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Driver;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/super-admin/dashboard",
     *     summary="Get admin dashboard statistics",
     *     description="Returns counts, orders and revenue statistics for the dashboard.",
     *     tags={"Admin Dashboard"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", nullable=true, example=null),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="counts",
     *                     type="object",
     *                     @OA\Property(property="drivers", type="integer", example=12),
     *                     @OA\Property(property="clients", type="integer", example=100),
     *                     @OA\Property(property="dispatchers", type="integer", example=5),
     *                     @OA\Property(property="admins", type="integer", example=2),
     *                 ),
     *                 @OA\Property(
     *                     property="orders",
     *                     type="object",
     *                     @OA\Property(property="today", type="integer", example=15),
     *                     @OA\Property(property="week", type="integer", example=120),
     *                     @OA\Property(property="month", type="integer", example=450),
     *                     @OA\Property(property="total", type="integer", example=1200),
     *                 ),
     *                 @OA\Property(
     *                     property="revenue",
     *                     type="object",
     *                     @OA\Property(property="today", type="number", format="float", example=250000.50),
     *                     @OA\Property(property="week", type="number", format="float", example=1200000.00),
     *                     @OA\Property(property="month", type="number", format="float", example=4000000.00),
     *                     @OA\Property(property="total", type="number", format="float", example=15000000.00),
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function dashboard()
    {
        return $this->response(data: [
            'counts' => [
                'drivers'     => Driver::count(),
                'clients'     => User::where('role', 'client')->count(),
                'dispatchers' => User::where('role', 'dispatcher')->count(),
                'admins'      => User::where('role', 'admin')->count(),
            ],
            'orders' => [
                'today'  => Order::whereDate('created_at', Carbon::today())->count(),
                'week'   => Order::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
                'month'  => Order::whereMonth('created_at', Carbon::now()->month)->count(),
                'total'  => Order::count(),
            ],
            'revenue' => [
                'today'  => Order::where('status', OrderStatus::Completed)->whereDate('created_at', Carbon::today())->sum('driver_payment'),
                'week'   => Order::where('status', OrderStatus::Completed)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('driver_payment'),
                'month'  => Order::where('status', OrderStatus::Completed)->whereMonth('created_at', Carbon::now()->month)->sum('driver_payment'),
                'total'  => Order::where('status', OrderStatus::Completed)->sum('driver_payment'),
            ],
        ], status: 200);
    }
}
