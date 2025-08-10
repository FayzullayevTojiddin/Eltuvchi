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

            $order->histories()->create([
                'status' => OrderStatus::Created,
                'changed_by_id' => $user->id,
                'changed_by_type' => get_class($user),
                'description' => 'Order created',
            ]);

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