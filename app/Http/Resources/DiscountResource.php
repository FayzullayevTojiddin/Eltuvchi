<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @OA\Schema(
 *     schema="Discount",
 *     type="object",
 *     title="Discount Resource",
 *     description="Discount details",
 *     @OA\Property(property="id", type="integer", example=7),
 *     @OA\Property(property="title", type="string", example="Loyalty Program"),
 *     @OA\Property(property="type", type="string", example="percentage", description="Discount type (percentage or fixed)"),
 *     @OA\Property(property="value", type="number", format="float", example=15, description="Discount value in percent or currency"),
 *     @OA\Property(property="points", type="integer", example=100, description="Points needed to get this discount"),
 *     @OA\Property(property="icon", type="string", nullable=true, example="https://example.com/icons/discount.png")
 * )
 */
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
            'icon'   => $this->product?->icon_type ? url(Storage::url($this->icon)) : null,
        ];
    }
}