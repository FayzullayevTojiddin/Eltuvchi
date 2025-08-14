<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User Resource",
 *     description="Authenticated user information",
 *     @OA\Property(property="id", type="integer", example=45),
 *     @OA\Property(property="role", type="string", example="client", description="User role (client, driver, admin, etc.)"),
 *     @OA\Property(property="email", type="string", format="email", nullable=true, example="user@example.com"),
 *     @OA\Property(property="telegram_id", type="string", nullable=true, example="123456789"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-14 12:00:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-14 12:30:00"),
 *     @OA\Property(
 *         property="client",
 *         ref="#/components/schemas/Client",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="driver",
 *         ref="#/components/schemas/Driver",
 *         nullable=true
 *     )
 * )
 */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'role' => $this->role,
            'email' => $this->email,
            'telegram_id' => $this->telegram_id,
            'created_at' => $this->created_at ? $this->created_at->toDateTimeString() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toDateTimeString() : null,
            'client' => new ClientResource($this->whenLoaded('client')),
            'driver' => new DriverResource($this->whenLoaded('driver')),
        ];
    }
}