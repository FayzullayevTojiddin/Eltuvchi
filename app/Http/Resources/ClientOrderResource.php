<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ClientOrder",
 *     type="object",
 *     title="Client Order Resource",
 *     description="Order details for a client",
 *     @OA\Property(property="id", type="integer", example=102),
 *     @OA\Property(property="status", type="string", example="pending", description="Order status"),
 *     @OA\Property(property="date", type="string", format="date", example="2025-08-15"),
 *     @OA\Property(property="time", type="string", example="14:30"),
 *     @OA\Property(property="passengers", type="integer", example=3),
 *     @OA\Property(property="phone", type="string", example="+998901234567"),
 *     @OA\Property(property="optional_phone", type="string", nullable=true, example="+998935551122"),
 *     @OA\Property(property="note", type="string", nullable=true, example="Please arrive 10 minutes early"),
 *     @OA\Property(property="price_order", type="number", format="float", example=250000),
 *     @OA\Property(property="client_deposit", type="number", format="float", example=50000),
 *     @OA\Property(property="discount_percent", type="number", format="float", example=10),
 *     @OA\Property(property="discount_summ", type="number", format="float", example=25000),
 *     @OA\Property(
 *         property="driver",
 *         ref="#/components/schemas/Driver",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="route",
 *         ref="#/components/schemas/Route",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="review",
 *         ref="#/components/schemas/Review",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="histories",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/OrderHistory")
 *     )
 * )
 */
class ClientOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'date' => $this->date,
            'time' => $this->time,
            'passengers' => $this->passengers,
            'phone' => $this->phone,
            'optional_phone' => $this->optional_phone,
            'note' => $this->note,
            'price_order' => $this->price_order,
            'client_deposit' => $this->client_deposit,
            'discount_percent' => $this->discount_percent,
            'discount_summ' => $this->discount_summ,
            'driver' => new DriverResource($this->whenLoaded('driver')),
            'route' => new RouteResource($this->whenLoaded('route')),
            'review' => new ReviewResource($this->whenLoaded('review')),
            'histories' => OrderHistoryResource::collection($this->whenLoaded('histories'))
        ];
    }
}