<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Client",
 *     type="object",
 *     title="Client Resource",
 *     description="Client details with balance, points, and status",
 *     @OA\Property(property="id", type="integer", example=12),
 *     @OA\Property(property="user_id", type="integer", example=101),
 *     @OA\Property(property="status", type="string", example="active"),
 *     @OA\Property(property="balance", type="number", format="float", example=150000),
 *     @OA\Property(property="points", type="integer", example=300),
 *     @OA\Property(
 *         property="settings",
 *         type="object",
 *         nullable=true,
 *         example={"notifications": true, "language": "uz"}
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-14 12:00:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-14 13:45:00"),
 *     @OA\Property(
 *         property="user",
 *         ref="#/components/schemas/User",
 *         nullable=true
 *     )
 * )
 */
class ClientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'balance' => $this->balance,
            'points' => $this->points,
            'settings' => $this->settings,
            'created_at' => $this->created_at ? $this->created_at->toDateTimeString() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toDateTimeString() : null,
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}