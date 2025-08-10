<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientDiscountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->discount->id,
            'title' => $this->discount->title,
            'type' => $this->discount->type,
            'value' => $this->discount->value,
            'points' => $this->discount->points,
            'icon' => $this->discount->icon,
            'used' => $this->used,
        ];
    }
}