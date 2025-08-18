<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminDispatcherResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     schema="AdminDispatcher",
     *     type="object",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="user_id", type="integer"),
     *     @OA\Property(property="taxopark_id", type="integer"),
     *     @OA\Property(property="full_name", type="string"),
     *     @OA\Property(property="status", type="boolean"),
     *     @OA\Property(property="details", type="array", @OA\Items(type="string")),
     *     @OA\Property(property="created_at", type="string", format="date-time"),
     *     @OA\Property(property="updated_at", type="string", format="date-time"),
     *     @OA\Property(property="user", type="object"),
     *     @OA\Property(property="taxopark", type="object")
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'user_id' => $this->user_id,
            'taxopark_id' => $this->taxopark_id,
            'status' => $this->status,
            'details' => $this->details,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'user' => $this->whenLoaded('user'),
            'taxopark' => $this->whenLoaded('taxopark'),
        ];
    }
}