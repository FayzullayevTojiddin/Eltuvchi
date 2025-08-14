<?php

namespace App\Http\Controllers;

use App\Http\Resources\DiscountResource;
use App\Models\ClientDiscount;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientMarketController extends Controller
{
    public function index()
    {
        $discounts = Discount::where('status', Discount::STATUS_ACTIVE)->get();
        return $this->response(DiscountResource::collection($discounts), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'discount_id' => 'required|integer|exists:discounts,id',
        ]);
        $discount = Discount::where('id', $validated['discount_id'])
                            ->where('status', Discount::STATUS_ACTIVE)
                            ->first();
        if (!$discount) {
            return $this->error([], 404, 'Discount not found or inactive.');
        }
        $client = Auth::user()->client;
        $pointsRequired = $discount->points ?? 0;
        if ($pointsRequired > 0) {
            $canSubtract = $client->subtractPoints($pointsRequired, "Purchased discount ID {$discount->id}");
            if (!$canSubtract) {
                return $this->error([], 400, 'You do not have enough points to purchase this discount.');
            }
        }
        $clientDiscount = ClientDiscount::create([
            'client_id' => $client->id,
            'discount_id' => $discount->id,
        ]);
        return $this->success(new DiscountResource($discount), 201, 'Discount purchased successfully.');
    }
}
