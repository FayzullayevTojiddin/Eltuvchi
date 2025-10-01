<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth",
     *     tags={"Authentication"},
     *     summary="Telegram orqali autentifikatsiya",
     *     description="Telegram WebApp orqali olingan initData ni yuborib foydalanuvchini autentifikatsiya qiladi.
     *                  Agar foydalanuvchi mavjud bo'lmasa — yangi client sifatida yaratiladi.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"initData"},
     *             @OA\Property(property="initData", type="string", example="query_id=AAE...&user=%7B%22id%22%3A123456%2C%22first_name%22%3A%22John%22%7D&hash=...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Muvaffaqiyatli autentifikatsiya",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="token", type="string", example="1|vZ6J3D..."),
     *             @OA\Property(property="role", type="string", example="client")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Noto‘g‘ri initData",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="invalid initData")
     *         )
     *     )
     * )
     */
    public function telegramAuth(Request $request)
    {
        $initData = $request->input('initData');

        $data = TelegramService::validateInitData($initData, env('TELEGRAM_BOT_TOKEN') || "null");
        if (!$data || !isset($data['user'])) {
            return response()->json(['status' => 'invalid initData'], 401);
        }

        $tgUser = is_array($data['user']) ? $data['user'] : json_decode($data['user'], true);
        $telegramId = $tgUser['id'];

        $user = User::where('telegram_id', $telegramId)->first();

        if (!$user) {
            $user = User::create([
                'name'        => $tgUser['first_name'] ?? 'Telegram User',
                'telegram_id' => $telegramId,
                'username'    => $tgUser['username'] ?? null,
                'photo_url'   => $tgUser['photo_url'] ?? null,
                'role'        => 'client',
                'password'    => Hash::make(uniqid()),
            ]);

            $settings = [
                'full_name'     => trim(($tgUser['first_name'] ?? '') . ' ' . ($tgUser['last_name'] ?? '')),
                'phone_number'  => null,
                'notifications' => true,
                'night_mode'    => false,
                'language'      => $tgUser['language_code'] ?? 'uz',
            ];

            Client::create([
                'user_id' => $user->id,
                'status'  => "active",
                'balance' => 0,
                'points'  => 0,
                'settings'=> $settings,
            ]);
        }

        $token = $user->createToken('telegram')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token'  => $token,
            'role'   => $user->role,
        ]);
    }
}
