<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Driver",
 *     type="object",
 *     title="Driver Resource",
 *     description="Driver model resource representation",
 *     @OA\Property(property="id", type="integer", example=1, description="Driver ID"),
 *     @OA\Property(property="user_id", type="integer", example=5, description="Related user ID"),
 *     @OA\Property(property="status", type="string", example="active", description="Driver status"),
 *     @OA\Property(property="balance", type="number", format="float", example=150000, description="Driver balance"),
 *     @OA\Property(property="points", type="integer", example=250, description="Driver reward points"),
 *     @OA\Property(property="details", type="object", nullable=true, description="Additional driver details"),
 *     @OA\Property(property="settings", type="object", nullable=true, description="Driver settings"),
 *     @OA\Property(property="taxopark_id", type="integer", example=2, description="Taxopark ID"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-14T10:20:30", description="Creation timestamp"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-14T11:20:30", description="Last update timestamp"),
 *     @OA\Property(
 *         property="user",
 *         ref="#/components/schemas/User",
 *         description="Related user data"
 *     ),
 *     @OA\Property(
 *         property="taxopark",
 *         ref="#/components/schemas/Taxopark",
 *         description="Related taxopark data"
 *     ),
 *     @OA\Property(
 *         property="orders",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Order"),
 *         description="List of driver's orders"
 *     )
 * )
 */
class DriverResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'user_id'   => $this->user_id,
            'status'    => $this->status,
            'balance'   => $this->balance,
            'points'    => $this->points,
            'details'   => $this->details,
            'settings'  => $this->settings,
            'taxopark_id' => $this->taxopark_id,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'user'      => new UserResource($this->whenLoaded('user')),
            'taxopark'  => new TaxoparkResource($this->whenLoaded('taxopark')),
            'orders'    => OrderResource::collection($this->whenLoaded('orders')),
        ];
    }
}