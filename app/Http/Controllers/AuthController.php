<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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
     *             @OA\Property(property="role", type="string", example="client"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="telegram_id", type="string", example="123456789")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="initData yuborilmagan",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="initData majburiy")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Noto'g'ri initData",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Noto'g'ri autentifikatsiya ma'lumotlari")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server xatosi",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Autentifikatsiya jarayonida xatolik yuz berdi")
     *         )
     *     )
     * )
     */
    public function telegramAuth(Request $request)
    {
        try {
            $initData = $request->input('initData');
            
            if (empty($initData)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'initData majburiy'
                ], 400);
            }

            $botToken = config('services.telegram.bot_token');
            
            if (empty($botToken)) {
                Log::error('TELEGRAM_BOT_TOKEN sozlanmagan');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Autentifikatsiya xizmati sozlanmagan'
                ], 500);
            }

            $data = TelegramService::validateInitData($initData, $botToken);
            
            if (!$data || !isset($data['user'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Noto\'g\'ri autentifikatsiya ma\'lumotlari'
                ], 401);
            }

            $tgUser = is_array($data['user']) 
                ? $data['user'] 
                : json_decode($data['user'], true);

            if (!isset($tgUser['id'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Foydalanuvchi ma\'lumotlari noto\'g\'ri'
                ], 401);
            }

            $telegramId = (string) $tgUser['id'];

            $user = User::where('telegram_id', $telegramId)->first();

            if (!$user) {
                $user = $this->createNewUser($tgUser, $telegramId);
            } else {
                $this->updateUserInfo($user, $tgUser);
            }

            $token = $user->createToken('telegram', ['*'], now()->addDays(30))->plainTextToken;

            return response()->json([
                'status' => 'success',
                'token' => $token,
                'role' => $user->role,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'telegram_id' => $user->telegram_id,
                    'username' => $user->username,
                    'photo_url' => $user->photo_url,
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Telegram autentifikatsiya xatosi: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Autentifikatsiya jarayonida xatolik yuz berdi'
            ], 500);
        }
    }
    private function createNewUser(array $tgUser, string $telegramId): User
    {
        $user = User::create([
            'name' => trim($tgUser['first_name'] ?? 'Telegram User'),
            'telegram_id' => $telegramId,
            'username' => $tgUser['username'] ?? null,
            'photo_url' => $tgUser['photo_url'] ?? null,
            'role' => 'client',
            'password' => Hash::make(uniqid('tg_', true)),
        ]);

        $settings = [
            'full_name' => trim(
                ($tgUser['first_name'] ?? '') . ' ' . ($tgUser['last_name'] ?? '')
            ),
            'phone_number' => null,
            'notifications' => true,
            'night_mode' => false,
            'language' => $tgUser['language_code'] ?? 'uz',
        ];

        Client::create([
            'user_id' => $user->id,
            'status' => 'new',
            'balance' => 0,
            'points' => 0,
            'settings' => $settings,
        ]);

        return $user;
    }

    private function updateUserInfo(User $user, array $tgUser): void
    {
        $updateData = [];

        $newName = trim($tgUser['first_name'] ?? '');
        if ($newName && $user->name !== $newName) {
            $updateData['name'] = $newName;
        }
        
        $newUsername = $tgUser['username'] ?? null;
        if ($newUsername && $user->username !== $newUsername) {
            $updateData['username'] = $newUsername;
        }

        $newPhotoUrl = $tgUser['photo_url'] ?? null;
        if ($newPhotoUrl && $user->photo_url !== $newPhotoUrl) {
            $updateData['photo_url'] = $newPhotoUrl;
        }

        if (!empty($updateData)) {
            $user->update($updateData);
        }
    }
}