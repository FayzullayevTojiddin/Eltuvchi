<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="OrderHistory",
 *     type="object",
 *     title="Order History Resource",
 *     description="Order status changes history",
 *     @OA\Property(property="id", type="integer", example=1, description="History record ID"),
 *     @OA\Property(property="status", type="string", example="pending", description="Order status at this history record"),
 *     @OA\Property(property="description", type="string", example="Order created by client", description="Reason or note for the status change"),
 *     @OA\Property(property="changed_by_type", type="string", example="client", description="Who changed the status (client, driver, admin)"),
 *     @OA\Property(property="changed_by_id", type="integer", example=42, description="ID of the user who changed the status"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-14 12:30:45", description="When this history record was created")
 * )
 */
class OrderHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->value,
            'description' => $this->description,
            'changed_by_type' => $this->changed_by_type,
            'changed_by_id' => $this->changed_by_id,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}