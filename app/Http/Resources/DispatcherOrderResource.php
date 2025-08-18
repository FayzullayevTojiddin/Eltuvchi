<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DispatcherOrderResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     schema="DispatcherOrder",
     *     type="object",
     *     title="Dispatcher Order",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="client_id", type="integer"),
     *     @OA\Property(property="driver_id", type="integer", nullable=true),
     *     @OA\Property(property="route_id", type="integer"),
     *     @OA\Property(property="passengers", type="integer"),
     *     @OA\Property(property="date", type="string", format="date"),
     *     @OA\Property(property="time", type="string"),
     *     @OA\Property(property="price_order", type="number", format="float"),
     *     @OA\Property(property="client_deposit", type="number", format="float"),
     *     @OA\Property(property="driver_payment", type="number", format="float", nullable=true),
     *     @OA\Property(property="discount_percent", type="integer", nullable=true),
     *     @OA\Property(property="discount_summ", type="number", format="float", nullable=true),
     *     @OA\Property(property="phone", type="string"),
     *     @OA\Property(property="optional_phone", type="string", nullable=true),
     *     @OA\Property(property="note", type="string", nullable=true),
     *     @OA\Property(property="status", type="string"),
     *     @OA\Property(property="created_at", type="string", format="date-time"),
     *     @OA\Property(property="updated_at", type="string", format="date-time"),
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'client_id' => $this->client_id,
            'driver_id' => $this->driver_id,
            'route_id' => $this->route_id,
            'passengers' => $this->passengers,
            'date' => $this->date->toDateString(),
            'time' => $this->time,
            'price_order' => $this->price_order,
            'client_deposit' => $this->client_deposit,
            'driver_payment' => $this->driver_payment,
            'discount_percent' => $this->discount_percent,
            'discount_summ' => $this->discount_summ,
            'phone' => $this->phone,
            'optional_phone' => $this->optional_phone,
            'note' => $this->note,
            'status' => $this->status->value,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'client' => $this->whenLoaded('client'),
            'driver' => $this->whenLoaded('driver'),
            'route' => $this->whenLoaded('route'),
        ];
    }
}