<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class DriverProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'status'      => (bool) $this->status,
            'icon_type'   => Storage::url($this->product->icon_type),
            'points'      => $this->product->points,
            'title'       => $this->product->title,
            'description' => $this->product->description,
            'created_at'  => $this->created_at?->toDateTimeString(),
        ];
    }
}
