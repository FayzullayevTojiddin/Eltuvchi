<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @OA\Schema(
 *     schema="PointHistory",
 *     type="object",
 *     title="Point History Resource",
 *     description="Foydalanuvchi point tarixini qaytaradi",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="points", type="integer", example=50),
 *     @OA\Property(property="description", type="string", example="Referral bonus"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-14 16:00:00")
 * )
 */

class PointHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'points' => $this->points,
            'description' => $this->description,
            'created_at' => $this->created_at
        ];
    }
}
