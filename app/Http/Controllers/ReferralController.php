<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReferralResource;
use App\Models\Referral;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/referrals",
     *     summary="Get referral statistics and points",
     *     tags={"Referrals"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Referral statistics retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Referral statistics and points retrieved successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="statistics",
     *                     type="object",
     *                     @OA\Property(property="total", type="integer", example=10),
     *                     @OA\Property(property="this_month", type="integer", example=3),
     *                     @OA\Property(property="this_week", type="integer", example=1)
     *                 ),
     *                 @OA\Property(
     *                     property="referrers",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Referral")
     *                 ),
     *                 @OA\Property(property="points", type="integer", example=100),
     *                    @OA\Property(
     *                         property="point_histories",
     *                         type="array",
     *                         @OA\Items(ref="#/components/schemas/PointHistory")
     *                    )
     *                 )
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
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $referralsQuery = Referral::where('referred_by', $user->id);

        $statistics = [
            'total' => (clone $referralsQuery)->count(),
            'this_month' => (clone $referralsQuery)
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
            'this_week' => (clone $referralsQuery)
                ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->count(),
        ];

        $referrers = ReferralResource::collection(
            $referralsQuery->with('owner')->latest()->get()
        );

        $connected = $user->connected();

        $points = $connected ? $connected->points : 0;
        $pointHistories = $connected ? $connected->pointHistories()->latest()->get() : collect();

        return $this->success([
            'statistics'      => $statistics,
            'referrers'       => $referrers,
            'points'          => $points,
            'point_histories' => $pointHistories,
        ], 200, 'Referral statistics and points retrieved successfully.');
    }

    /**
     * @OA\Post(
     *     path="/api/referrals",
     *     summary="Add a referral using a promo code",
     *     tags={"Referrals"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="promo_code", type="string", example="PROMO123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Referral added successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Referral added successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid promo code",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid promo code")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="User already referred",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You have already been referred")
     *         )
     *     )
     * )
     */
    public function referred(Request $request)
    {
        $user = $request->user();
        $promoCode = $request->input('promo_code');

        $referrer = User::where('promo_code', $promoCode)->first();

        if (!$referrer) {
            return response()->json(['message' => 'Invalid promo code'], 400);
        }

        $existingReferral = Referral::where('user_id', $user->id)->first();
        if ($existingReferral) {
            return response()->json(['message' => 'You have already been referred'], 409);
        }

        Referral::create([
            'user_id' => $user->id,
            'promo_code' => $user->promo_code,
            'referred_by' => $referrer->id,
        ]);

        $points = (int) env('REFERRAL_POINT', 0);
        $connected = $user->connected();
        if ($connected && $points > 0) {
            $connected->addPoints($points, 'Referral bonus');
        }

        return response()->json(['message' => 'Referral added successfully'], 201);
    }
}