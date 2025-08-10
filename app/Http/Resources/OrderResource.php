<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'discount' => $this->discount ?? null,
        ];
    }
}