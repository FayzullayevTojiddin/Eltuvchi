<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Resources\OrderReviewResource;
use App\Models\Order;
use App\Models\OrderReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderReviewController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/client/orders/{order}/review",
     *     summary="Submit a review for a completed order",
     *     tags={"Client Orders"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="order",
     *         in="path",
     *         required=true,
     *         description="Order ID to review",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="rating", type="integer", minimum=1, maximum=5, example=5, description="Rating for the order"),
     *             @OA\Property(property="comment", type="string", example="Great driver!", description="Optional comment for the review")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Review created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Review created successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/OrderReview")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden action",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You are not allowed to review this order.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Review already exists",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You have already reviewed this order."),
     *             @OA\Property(property="data", ref="#/components/schemas/OrderReview")
     *         )
     *     )
     * )
     */
    public function client_review(Request $request, Order $order)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $client = Auth::user()->client;

        if ($order->client_id !== $client->id) {
            return $this->error([], 403, 'You are not allowed to review this order.');
        }

        if ($order->status !== OrderStatus::Completed) {
            return $this->error([], 403, 'Order is not completed yet.');
        }

        if (empty($order->driver_id)) {
            return $this->error([], 403, 'Driver is not assigned yet.');
        }

        $existingReview = OrderReview::where('order_id', $order->id)
            ->where('client_id', $client->id)
            ->first();

        if ($existingReview) {
            return $this->error(new OrderReviewResource($existingReview), 409, 'You have already reviewed this order.');
        }

        $review = OrderReview::create([
            'order_id' => $order->id,
            'client_id' => $client->id,
            'score' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return $this->success(new OrderReviewResource($review), 201, 'Review created successfully.');
    }
}