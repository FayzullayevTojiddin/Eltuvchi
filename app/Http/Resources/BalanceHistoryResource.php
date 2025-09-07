<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="BalanceHistory",
 *     type="object",
 *     title="Balance History Resource",
 *     description="Balance history entry representation",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="amount", type="number", format="float", example=150000),
 *     @OA\Property(
 *         property="type",
 *         type="string",
 *         example="plus",
 *         description="Transaction type (plus, minus)"
 *     ),
 *     @OA\Property(property="balance_after", type="number", format="float", example=350000),
 *     @OA\Property(property="description", type="string", nullable=true, example="Deposit from bank"),
 *     @OA\Property(
 *         property="user",
 *         type="object",
 *         description="User who changed the balance",
 *         @OA\Property(property="id", type="integer", example=42),
 *         @OA\Property(property="name", type="string", example="John Doe")
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-14 12:34:56"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-14 12:34:56")
 * )
 */
class BalanceHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'amount'        => $this->amount,
            'type'          => $this->type,
            'balance_after' => $this->balance_after,
            'description'   => $this->description,
            'user'          => $this->user ? [
                'id'   => $this->user->id,
                'name' => $this->user->display_name,
            ] : null,
            'created_at'    => $this->created_at?->toDateTimeString(),
            'updated_at'    => $this->updated_at?->toDateTimeString(),
        ];
    }
}