<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Taxopark",
 *     type="object",
 *     title="TaxoPark",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(
 *         property="region",
 *         ref="#/components/schemas/Region"
 *     ),
 *     @OA\Property(property="status", type="string", example="active"),
 *     @OA\Property(property="name", type="string", example="Toshkent Taxi")
 * )
 */
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
