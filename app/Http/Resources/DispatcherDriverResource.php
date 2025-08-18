<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DispatcherDriverResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     schema="DispatcherDriver",
     *     type="object",
     *     title="Dispatcher Driver",
     *     @OA\Property(property="id", type="integer"),
     *     @OA\Property(property="user_id", type="integer"),
     *     @OA\Property(property="taxopark_id", type="integer"),
     *     @OA\Property(property="status", type="string"),
     *     @OA\Property(property="balance", type="number", format="float"),
     *     @OA\Property(property="points", type="number", format="float"),
     *     @OA\Property(property="details", type="array", @OA\Items(type="object"), nullable=true),
     *     @OA\Property(property="settings", type="array", @OA\Items(type="object"), nullable=true),
     *     @OA\Property(property="created_at", type="string", format="date-time"),
     *     @OA\Property(property="updated_at", type="string", format="date-time"),
     *     @OA\Property(property="user", type="object", nullable=true),
     *     @OA\Property(property="taxopark", type="object", nullable=true),
     *     @OA\Property(property="orders", type="array", @OA\Items(ref="#/components/schemas/DispatcherOrder")),
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'taxopark_id' => $this->taxopark_id,
            'status' => $this->status,
            'balance' => $this->balance,
            'points' => $this->points,
            'details' => $this->details,
            'settings' => $this->settings,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'user' => $this->whenLoaded('user', fn() => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'phone' => $this->user->phone,
            ]),
            'taxopark' => $this->whenLoaded('taxopark', fn() => [
                'id' => $this->taxopark->id,
                'title' => $this->taxopark->title,
            ]),
            'orders' =>  DispatcherOrderResource::collection($this->whenLoaded('orders'))
        ];
    }
}