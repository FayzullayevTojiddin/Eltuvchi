<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientDiscountResource;
use App\Http\Resources\DiscountResource;
use App\Models\ClientDiscount;
use Illuminate\Support\Facades\Auth;

class ClientDiscountController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/client/my_discounts",
     *     summary="Get client's active discounts",
     *     tags={"Client"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of client's discounts",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/ClientDiscount")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $client = Auth::user()->client;
        $discounts = ClientDiscount::with('discount')
            ->where('client_id', $client->id)
            ->where('used', false)
            ->get();

        return $this->response(ClientDiscountResource::collection($discounts));
    }
}
