<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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