<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReferralResource;
use App\Models\Referral;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
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
            'statistics'     => $statistics,
            'referrers'      => $referrers,
            'points'         => $points,
            'point_histories'=> $pointHistories,
        ], 200, 'Referral statistics and points retrieved successfully.');
    }

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