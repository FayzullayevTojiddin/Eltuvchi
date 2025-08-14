<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\ClientResource;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/client/dashboard",
     *     summary="Get client dashboard info",
     *     tags={"Client"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Client dashboard retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="orders_count", type="integer", example=0),
     *                 @OA\Property(property="balance", type="number", example=0),
     *                 @OA\Property(property="points", type="integer", example=0)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function dashboard(Request $request)
    {
        $client = Auth::user()->client;

        $data = [
            'orders_count' => $client->orders()->count(),
            'balance'      => $client->balance,
            'points'       => $client->points,
        ];

        return $this->response($data);
    }
}