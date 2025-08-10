<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->value,
            'description' => $this->description,
            'changed_by_type' => $this->changed_by_type,
            'changed_by_id' => $this->changed_by_id,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
