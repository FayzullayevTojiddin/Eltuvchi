<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="AdminOrder",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="status", type="string", example="pending"),
 *     @OA\Property(property="scheduled_at", type="string", format="date-time", example="2025-08-20 14:00:00"),
 *     @OA\Property(property="passengers", type="integer", example=3),
 *     @OA\Property(property="price_summ", type="number", format="float", example=150000),
 *     @OA\Property(property="client_deposit", type="number", format="float", example=50000),
 *     @OA\Property(property="price_order", type="number", format="float", example=200000),
 *     @OA\Property(property="phone", type="string", example="+998901234567"),
 *     @OA\Property(property="optional_phone", type="string", nullable=true, example="+998931234567"),
 *     @OA\Property(property="note", type="string", nullable=true, example="Urgent order"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-15T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-16T10:00:00Z"),
 *     @OA\Property(property="client", ref="#/components/schemas/AdminClient"),
 *     @OA\Property(property="driver", ref="#/components/schemas/AdminDriver"),
 *     @OA\Property(property="route", ref="#/components/schemas/Route"),
 *     @OA\Property(property="histories", type="array", @OA\Items(ref="#/components/schemas/OrderHistory")),
 *     @OA\Property(property="review", ref="#/components/schemas/Review"),
 * )
 */
class AdminOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'status'        => $this->status,
            'scheduled_at'  => $this->scheduled_at,
            'passengers'    => $this->passengers,
            'price_summ'    => $this->price_summ,
            'client_deposit'=> $this->client_deposit,
            'price_order'   => $this->price_order,
            'phone'         => $this->phone,
            'optional_phone'=> $this->optional_phone,
            'note'          => $this->note,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
            'client'    => new AdminClientResource($this->whenLoaded('client')),
            'driver'    => new AdminDriverResource($this->whenLoaded('driver')),
            'route'     => new RouteResource($this->whenLoaded('route')),
            'histories' => OrderHistoryResource::collection($this->whenLoaded('histories')),
            'review'    => new ReviewResource($this->whenLoaded('review')),
        ];
    }
}