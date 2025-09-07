<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\ClientDiscount;
use App\Models\Order;
use App\Enums\OrderStatus;
use App\Http\Resources\OrderResource;
use App\Models\Route;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/client/orders",
     *     summary="Create a new order",
     *     tags={"Client Orders"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="route_id", type="integer", example=1),
     *             @OA\Property(property="passengers", type="integer", example=3),
     *             @OA\Property(property="date", type="string", format="date", example="2025-08-15"),
     *             @OA\Property(property="time", type="string", format="time", example="14:30"),
     *             @OA\Property(property="client_deposit", type="number", example=50000),
     *             @OA\Property(property="discount_id", type="integer", nullable=true, example=2),
     *             @OA\Property(property="phone", type="string", example="+998901234567"),
     *             @OA\Property(property="optional_phone", type="string", nullable=true, example="+998935551122"),
     *             @OA\Property(property="note", type="string", nullable=true, example="Please call on arrival")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Order created successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=111),
     *                 @OA\Property(property="status", type="string", example="created"),
     *                 @OA\Property(property="scheduled_at", type="string", format="date-time", nullable=true, example=null),
     *                 @OA\Property(property="passengers", type="integer", example=3),
     *                 @OA\Property(property="phone", type="string", example="+998901234567"),
     *                 @OA\Property(property="optional_phone", type="string", nullable=true, example="+998901234568"),
     *                 @OA\Property(property="note", type="string", nullable=true, example="Please call on arrival"),
     *                 @OA\Property(property="price_order", type="string", example="1107231.00"),
     *                 @OA\Property(property="client_deposit", type="string", example="50000.00"),
     *                 @OA\Property(property="discount_percent", type="integer", example=10),
     *                 @OA\Property(property="discount_summ", type="string", example="110723.10"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-14 11:43:28")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Insufficient balance",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Balance is insufficient for deposit payment.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=501,
     *         description="Discount not found for this client",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Not found this discount")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to create order",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Failed to create order.")
     *         )
     *     )
     * )
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $user = Auth::user();
        $client = $user->client;
        $discountPercent = 0;
        $discountSumm = 0;
        $clientDiscount = null;

        if ($request->filled('discount_id')) {
            $clientDiscount = ClientDiscount::where('client_id', $client->id)
                ->where('discount_id', $request->discount_id)
                ->where('used', false)
                ->with('discount')
                ->first();

            if(!$clientDiscount){
                return $this->error([], 501, 'Not found this discount');
            }

            if ($clientDiscount && $clientDiscount->discount) {
                if ($clientDiscount->discount->type === 'percent') {
                    $discountPercent = $clientDiscount->discount->value;
                } else {
                    $discountSumm = $clientDiscount->discount->value;
                }
            }
        }

        $priceOrder = $this->calculatePriceOrder($request->route_id, $request->passengers);

        if ($discountPercent > 0) {
            $discountSumm = ($priceOrder * $discountPercent) / 100;
        }

        $depositToCharge = round($request->client_deposit - $discountSumm, 2);
        if ($depositToCharge < 0) {
            $depositToCharge = 0;
        }

        if (!$client->subtractBalance($depositToCharge, "Order deposit payment")) {
            return $this->error('Balance is insufficient for deposit payment.', 400);
        }

        DB::beginTransaction();

        try {
            $order = Order::create([
                'client_id' => $client->id,
                'driver_id' => null,
                'route_id' => $request->route_id,
                'passengers' => $request->passengers,
                'date' => $request->date,
                'time' => $request->time,
                'price_order' => $priceOrder,
                'client_deposit' => $request->client_deposit,
                'discount_percent' => $discountPercent,
                'discount_summ' => $discountSumm,
                'phone' => $request->phone,
                'optional_phone' => $request->optional_phone,
                'note' => $request->note,
                'status' => OrderStatus::Created->value,
            ]);

            if ($clientDiscount) {
                $clientDiscount->update(['used' => true]);
            }

            DB::commit();

            return $this->success(new OrderResource($order), 200, 'Order created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->error('Failed to create order.', 500);
        }
    }

    protected function calculatePriceOrder(int $routeId, int $passengers): float
    {
        $route = Route::findOrFail($routeId);
        return $route->price_in * $passengers;
    }
}