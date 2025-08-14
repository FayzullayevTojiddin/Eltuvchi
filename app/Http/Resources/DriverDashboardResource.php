<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

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