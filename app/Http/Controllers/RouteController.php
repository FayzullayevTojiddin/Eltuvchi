<?php

namespace App\Http\Controllers;

use App\Enums\RouteStatus;
use App\Http\Resources\RouteResource;
use App\Models\Route;
use Illuminate\Support\Facades\Cache;

/**
 * @OA\Get(
 *     path="/api/routes/check/{from}/{to}",
 *     summary="Check for an active route between two taxoparks",
 *     tags={"Routes"},
 *     @OA\Parameter(
 *         name="from",
 *         in="path",
 *         description="ID of the starting taxopark",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="to",
 *         in="path",
 *         description="ID of the destination taxopark",
 *         required=true,
 *         @OA\Schema(type="integer", example=2)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Route found successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="data",
 *                 ref="#/components/schemas/Route"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Route not found",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="error", type="string", example="Route not found."),
 *             @OA\Property(property="data", type="array", @OA\Items())
 *         )
 *     )
 * )
 */
class RouteController extends Controller
{
    public function check(int $from, int $to)
    {
        $route = Route::with(['fromTaxopark', 'toTaxopark'])
                ->where('taxopark_from_id', $from)
                ->where('taxopark_to_id', $to)
                ->where('status', RouteStatus::ACTIVE->value)
                ->first();
        if (!$route) {
            return $this->error([], 404, 'Route not found.');
        }
        return $this->response(new RouteResource($route));
    }
}