<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Route",
 *     type="object",
 *     title="Route Resource",
 *     description="Route details including taxopark, pricing and status",
 *     @OA\Property(property="id", type="integer", example=1, description="Route ID"),
 *     @OA\Property(
 *         property="from",
 *         ref="#/components/schemas/Taxopark",
 *         description="Starting taxopark details"
 *     ),
 *     @OA\Property(
 *         property="to",
 *         ref="#/components/schemas/Taxopark",
 *         description="Destination taxopark details"
 *     ),
 *     @OA\Property(property="status", type="string", example="active", description="Route status"),
 *     @OA\Property(property="deposit_client", type="number", format="float", example=50000, description="Client deposit amount for this route"),
 *     @OA\Property(property="distance_km", type="number", format="float", example=12.5, description="Distance in kilometers"),
 *     @OA\Property(property="price_in", type="number", format="float", example=120000, description="Price for the route"),
 *     @OA\Property(property="fee_per_client", type="number", format="float", example=10000, description="Fee per client")
 * )
 */
class RouteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'from' => new TaxoParkResource($this->fromTaxopark),
            'to' => new TaxoParkResource($this->toTaxopark),
            'status' => $this->status,
            'deposit_client' => $this->deposit_client,
            'distance_km' => $this->distance_km,
            'price_in' => $this->price_in,
            'fee_per_client' => $this->fee_per_client,
        ];
    }
}
