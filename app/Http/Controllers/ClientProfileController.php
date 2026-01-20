<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientProfileController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/client/me",
     *     summary="Get authenticated client profile",
     *     tags={"Client"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Client profile retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Client profile retrieved successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=45),
     *                 @OA\Property(property="role", type="string", example="client"),
     *                 @OA\Property(property="telegram_id", type="string", example="123456789"),
     *                 @OA\Property(
     *                     property="settings",
     *                     type="object",
     *                     @OA\Property(property="notifications", type="boolean", example=true),
     *                     @OA\Property(property="night_mode", type="boolean", example=false),
     *                     @OA\Property(property="language", type="string", example="uz")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Client not found.")
     *         )
     *     )
     * )
     */
    public function me(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->client) {
            return $this->error([], 404, 'Client not found.');
        }

        return $this->success([
            'id'         => $user->id,
            'client_id' => $user->client->id,
            'role'       => $user->role,
            'telegram_id'=> $user->telegram_id,
            'settings'   => $user->client->settings,
            'balance' => $user->client->balance,
        ], 200, 'Client profile retrieved successfully.');
    }

    /**
     * @OA\Put(
     *     path="/api/client/me",
     *     summary="Update authenticated client profile",
     *     tags={"Client"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="full_name", type="string", example="John Doe"),
     *             @OA\Property(property="phone_number", type="string", example="+998901234567"),
     *             @OA\Property(property="notifications", type="boolean", example=true),
     *             @OA\Property(property="night_mode", type="boolean", example=false),
     *             @OA\Property(property="language", type="string", example="uz")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client profile updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Client profile updated successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=45),
     *                 @OA\Property(property="role", type="string", example="client"),
     *                 @OA\Property(property="telegram_id", type="string", example="123456789"),
     *                 @OA\Property(
     *                     property="settings",
     *                     type="object",
     *                     @OA\Property(property="notifications", type="boolean", example=true),
     *                     @OA\Property(property="night_mode", type="boolean", example=false),
     *                     @OA\Property(property="language", type="string", example="uz")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Client not found.")
     *         )
     *     )
     * )
     */
    public function edit(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->client) {
            return $this->error([], 404, 'Client not found.');
        }
        $validated = $request->validate([
            'full_name'     => 'nullable|string|max:255',
            'phone_number'  => 'nullable|string|max:20',
            'notifications' => 'boolean',
            'night_mode'    => 'boolean',
            'language'      => 'nullable|string|in:uz,ru,en',
        ]);

        $settings = $user->client->settings ?? [];

        $settings = array_merge($settings, $validated);

        $user->client->settings = $settings;
        $user->client->save();

        return $this->success([
            'id'         => $user->id,
            'role'       => $user->role,
            'telegram_id'=> $user->telegram_id,
            'settings'   => $user->client->settings,
        ], 200, 'Client profile updated successfully.');
    }
}
