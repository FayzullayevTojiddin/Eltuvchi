<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RouteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'from' => new TaxoParkResource($this->whenLoaded('fromTaxopark')),
            'to' => new TaxoParkResource($this->whenLoaded('toTaxopark')),
            'status' => $this->status->value,
            'deposit_client' => $this->deposit_client,
            'distance_km' => $this->distance_km,
            'price_in' => $this->price_in,
            'fee_per_client' => $this->fee_per_client,
        ];
    }
}