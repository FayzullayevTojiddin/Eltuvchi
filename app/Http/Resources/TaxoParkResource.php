<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaxoParkResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'region' => new RegionResource($this->whenLoaded('region')),
            'status' => $this->status,
            'name' => $this->name,
        ];
    }
}
