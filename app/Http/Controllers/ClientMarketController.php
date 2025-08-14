<?php

namespace App\Http\Controllers;

use App\Http\Resources\DiscountResource;
use App\Models\ClientDiscount;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientMarketController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/client/market",
     *     summary="List active discounts in the market",
     *     tags={"Client Market"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Active discounts retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Active discounts retrieved successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Discount")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $discounts = Discount::where('status', Discount::STATUS_ACTIVE)->get();
        return $this->response(DiscountResource::collection($discounts), 200);
    }

    /**
     * @OA\Post(
     *     path="/api/client/market",
     *     summary="Purchase a discount using client points",
     *     tags={"Client Market"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="discount_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Discount purchased successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Discount purchased successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/Discount")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Not enough points",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You do not have enough points to purchase this discount.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Discount not found or inactive",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Discount not found or inactive.")
     *         )
     *     )
     * )
     */
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
