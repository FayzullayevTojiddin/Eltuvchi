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
    public function client_review(Request $request, Order $order)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $client = Auth::user()->client;

        // 1) Ownership: faqat order egasi yozishi mumkin
        if ($order->client_id !== $client->id) {
            return $this->error([], 403, 'You are not allowed to review this order.');
        }

        // 2) Status: faqat COMPLETED bo'lganda ruxsat
        if ($order->status !== OrderStatus::Completed) {
            return $this->error([], 403, 'Order is not completed yet.');
        }

        // 3) Driver tayinlangan bo'lishi kerak
        if (empty($order->driver_id)) {
            return $this->error([], 403, 'Driver is not assigned yet.');
        }

        // 4) Dublikatni tekshirish
        $existingReview = OrderReview::where('order_id', $order->id)
            ->where('client_id', $client->id)
            ->first();

        if ($existingReview) {
            return $this->error(new OrderReviewResource($existingReview), 409, 'You have already reviewed this order.');
        }

        // 5) Yaratish (requestdagi rating -> modeldagi score)
        $review = OrderReview::create([
            'order_id' => $order->id,
            'client_id' => $client->id,
            'score' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return $this->success(new OrderReviewResource($review), 201, 'Review created successfully.');
    }
}