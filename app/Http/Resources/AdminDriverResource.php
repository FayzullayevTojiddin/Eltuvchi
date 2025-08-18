<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminDriverResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     schema="AdminDriver",
     *     type="object",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="status", type="string"),
     *     @OA\Property(property="balance", type="integer"),
     *     @OA\Property(property="points", type="integer"),
     *     @OA\Property(property="details", type="object"),
     *     @OA\Property(property="settings", type="object"),
     *     @OA\Property(property="user", type="object"),
     *     @OA\Property(property="taxopark", type="object"),
     *     @OA\Property(property="orders_count", type="integer"),
     *     @OA\Property(property="created_at", type="string", format="date-time"),
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'status'     => $this->status,
            'balance'    => $this->balance,
            'points'     => $this->points,
            'details'    => $this->details,
            'settings'   => $this->settings,
            'taxopark'   => $this->whenLoaded('taxopark'),
            'user'       => $this->whenLoaded('user'),
            'orders_count' => $this->whenLoaded('orders', fn () => $this->orders->count()),
            'created_at' => $this->created_at,
        ];
    }
}