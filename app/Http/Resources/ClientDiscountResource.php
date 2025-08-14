<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ClientDiscount",
 *     type="object",
 *     title="Client Discount Resource",
 *     description="Discount information for a client",
 *     @OA\Property(property="id", type="integer", example=5),
 *     @OA\Property(property="title", type="string", example="Summer Sale"),
 *     @OA\Property(property="type", type="string", example="percentage", description="Discount type (percentage or fixed)"),
 *     @OA\Property(property="value", type="number", format="float", example=10, description="Discount value in percent or currency"),
 *     @OA\Property(property="icon", type="string", nullable=true, example="https://example.com/icons/discount.png")
 * )
 */
class ClientDiscountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->discount->id,
            'title' => $this->discount->title,
            'type' => $this->discount->type,
            'value' => $this->discount->value,
            'icon' => $this->discount->icon,
        ];
    }
}