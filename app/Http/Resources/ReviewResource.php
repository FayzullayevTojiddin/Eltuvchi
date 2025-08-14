<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Review",
 *     type="object",
 *     title="Review Resource",
 *     description="Review details for orders or items",
 *     @OA\Property(
 *         property="rating",
 *         type="integer",
 *         example=5,
 *         description="Rating given, e.g., from 1 to 5"
 *     ),
 *     @OA\Property(
 *         property="comment",
 *         type="string",
 *         nullable=true,
 *         example="Great service!",
 *         description="Optional comment for the review"
 *     )
 * )
 */
class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'rating' => $this->rating,
            'comment' => $this->comment,
        ];
    }
}