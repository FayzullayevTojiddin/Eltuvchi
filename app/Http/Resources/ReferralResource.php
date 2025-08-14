<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Referral",
 *     type="object",
 *     title="Referral Resource",
 *     description="Referral ma'lumotlarini qaytaruvchi resource",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=12,
 *         description="Referral egasining ID raqami"
 *     ),
 *     @OA\Property(
 *         property="full_name",
 *         type="string",
 *         nullable=true,
 *         example="John Doe",
 *         description="Referral bo‘yicha bog‘langan foydalanuvchi to‘liq ismi"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         example="2025-08-14 15:45:12",
 *         description="Referral yaratilgan vaqt"
 *     )
 * )
 */
class ReferralResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->owner->id,
            'full_name'  => $this->connected->settings->full_name ?? null,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}