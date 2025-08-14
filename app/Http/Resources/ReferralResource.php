<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReferralResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->owner->id,
            'full_name'       => $this->connected->settings->full_name ?? null,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}