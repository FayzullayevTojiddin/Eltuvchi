<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="OrderReview",
 *     type="object",
 *     title="Order Review Resource",
 *     description="Order review details",
 *     @OA\Property(property="id", type="integer", example=1, description="Review ID"),
 *     @OA\Property(property="order_id", type="integer", example=15, description="Related order ID"),
 *     @OA\Property(property="client_id", type="integer", example=3, description="Client ID who left the review"),
 *     @OA\Property(property="score", type="integer", example=5, description="Review score from 1 to 5"),
 *     @OA\Property(property="comment", type="string", example="Very satisfied with the service", description="Review comment"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-14 14:32:00", description="When the review was created"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-14 14:35:00", description="When the review was last updated")
 * )
 */
class OrderReviewResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'order_id'   => $this->order_id,
            'client_id'  => $this->client_id,
            'score'      => $this->score,
            'comment'    => $this->comment,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}