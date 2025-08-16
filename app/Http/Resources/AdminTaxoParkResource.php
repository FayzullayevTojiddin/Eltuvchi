<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminTaxoParkResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     schema="AdminTaxoParkResource",
     *     type="object",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="name", type="string"),
     *     @OA\Property(property="status", type="string"),
     *     @OA\Property(
     *         property="region",
     *         type="object",
     *         @OA\Property(property="id", type="integer"),
     *         @OA\Property(property="name", type="string")
     *     ),
     *     @OA\Property(
     *         property="drivers",
     *         type="array",
     *         @OA\Items(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="balance", type="integer"),
     *             @OA\Property(property="points", type="integer"),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="email", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Property(
     *         property="dispatchers",
     *         type="array",
     *         @OA\Items(type="object")
     *     ),
     *     @OA\Property(property="created_at", type="string", format="date-time"),
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'status'      => $this->status,
            'region'      => $this->whenLoaded('region'),
            'drivers'     => $this->whenLoaded('drivers', fn () => $this->drivers->map(function ($driver) {
                return [
                    'id'     => $driver->id,
                    'status' => $driver->status,
                    'balance'=> $driver->balance,
                    'points' => $driver->points,
                    'user'   => $driver->user,
                ];
            })),
            'dispatchers' => $this->whenLoaded('dispatchers'),
            'created_at'  => $this->created_at,
        ];
    }
}