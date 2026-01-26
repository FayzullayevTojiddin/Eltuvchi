<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     title="Product Resource",
 *     description="Product resource representation",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="icon_type", type="string", example="gift"),
 *     @OA\Property(property="points", type="integer", example=150),
 *     @OA\Property(property="title", type="string", example="Free Wash"),
 *     @OA\Property(property="description", type="string", example="One free car wash at partner stations."),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-16 12:30:00")
 * )
 */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'status'      => (bool) $this->status,
            'icon_type' => $this->product?->icon_type ? url(Storage::url($this->product->icon_type)) : null,
            'points'      => $this->points,
            'title'       => $this->title,
            'description' => $this->description,
            'created_at'  => $this->created_at?->toDateTimeString(),
        ];
    }
}