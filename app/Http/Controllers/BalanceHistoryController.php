<?php

namespace App\Http\Controllers;

use App\Http\Resources\BalanceHistoryResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BalanceHistoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/balance-history",
     *     summary="Get balance history for authenticated user (client or driver)",
     *     tags={"Balance History"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of balance history",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/BalanceHistory")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function balance_history(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'client') {
            $histories = $user->client->balanceHistories()->orderByDesc('created_at')->get();
        } elseif ($user->role === 'driver') {
            $histories = $user->driver->balanceHistories()->orderByDesc('created_at')->get();
        } else {
            return $this->error([], 403, 'Unauthorized user type.');
        }

        return $this->response(BalanceHistoryResource::collection($histories));
    }
}