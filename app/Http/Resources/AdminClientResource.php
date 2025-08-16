<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminClientResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     schema="AdminClient",
     *     type="object",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="status", type="string"),
     *     @OA\Property(property="balance", type="integer"),
     *     @OA\Property(property="points", type="integer"),
     *     @OA\Property(property="settings", type="object"),
     *     @OA\Property(property="user", type="object"),
     *     @OA\Property(property="discounts", type="array", @OA\Items(type="object")),
     *     @OA\Property(property="orders_count", type="integer"),
     *     @OA\Property(property="created_at", type="string", format="date-time"),
     *     @OA\Property(property="updated_at", type="string", format="date-time")
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'status'    => $this->status,
            'balance'   => $this->balance,
            'points'    => $this->points,
            'settings'  => $this->settings,
            'user'      => new UserResource($this->whenLoaded('user')),
            'discounts' => ClientDiscountResource::collection($this->whenLoaded('discounts')),
            'orders_count' => $this->whenLoaded('orders', fn () => $this->orders->count()),
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
        ];
    }
}