<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="DriverDashboard",
 *     type="object",
 *     title="Driver Dashboard Resource",
 *     description="Driver dashboard statistics and recent orders",
 *     @OA\Property(property="total_income", type="number", format="float", example=1200000, description="Total income earned by driver"),
 *     @OA\Property(property="completed_orders_count", type="integer", example=154, description="Number of completed orders"),
 *     @OA\Property(property="average_rating", type="number", format="float", example=4.8, description="Average rating of driver"),
 *     @OA\Property(
 *         property="recent_orders",
 *         type="array",
 *         description="List of recent orders",
 *         @OA\Items(ref="#/components/schemas/Order")
 *     )
 * )
 */
class DriverDashboardResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'total_income' => $this['total_income'],
            'completed_orders_count' => $this['completed_orders_count'],
            'average_rating' => $this['average_rating'],
            'recent_orders' => $this['recent_orders'],
        ];
    }
}