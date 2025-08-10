<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'     => $this->id,
            'title'  => $this->title,
            'type'   => $this->type,
            'value'  => $this->value,
            'points' => $this->points,
            'icon'   => $this->icon,
        ];
    }
}