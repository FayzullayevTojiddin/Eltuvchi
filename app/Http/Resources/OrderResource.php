<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Order",
 *     type="object",
 *     title="Order Resource",
 *     description="Detailed order information",
 *     @OA\Property(property="id", type="integer", example=101),
 *     @OA\Property(property="Client", ref="#/components/schemas/Client", nullable=true),
 *     @OA\Property(property="driver", ref="#/components/schemas/Driver", nullable=true),
 *     @OA\Property(property="route", ref="#/components/schemas/Route", nullable=true),
 *     @OA\Property(property="status", type="string", example="pending"),
 *     @OA\Property(property="scheduled_at", type="string", format="date-time", example="2025-08-15T14:30:00Z"),
 *     @OA\Property(property="passengers", type="integer", example=3),
 *     @OA\Property(property="phone", type="string", example="+998901234567"),
 *     @OA\Property(property="optional_phone", type="string", nullable=true, example="+998935551122"),
 *     @OA\Property(property="note", type="string", nullable=true, example="Please arrive 10 minutes early"),
 *     @OA\Property(property="price_order", type="number", format="float", example=250000),
 *     @OA\Property(property="client_deposit", type="number", format="float", example=50000),
 *     @OA\Property(property="discount_percent", type="number", format="float", example=10),
 *     @OA\Property(property="discount_summ", type="number", format="float", example=25000),
 *     @OA\Property(property="discount", type="string", nullable=true, example=null),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-14T12:00:00Z")
 * )
 */
class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'client' => new ClientResource($this->whenLoaded('client')),
            'driver' => new DriverResource($this->whenLoaded('driver')),
            'route' => new RouteResource($this->whenLoaded('route')),
            'status' => $this->status->value,
            'scheduled_at' => $this->scheduled_at?->toIso8601String(),
            'passengers' => $this->passengers,
            'phone' => $this->phone,
            'optional_phone' => $this->optional_phone,
            'note' => $this->note,
            'price_order' => $this->price_order,
            'client_deposit' => $this->client_deposit,
            'discount_percent' => $this->discount_percent ?? 0,
            'discount_summ' => $this->discount_summ ?? '0.00',
            'created_at' => $this->created_at ? $this->created_at->toDateTimeString() : null,
        ];
    }
}