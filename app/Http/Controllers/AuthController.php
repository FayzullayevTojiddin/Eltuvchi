<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function telegramAuth(Request $request)
    {
        $initData = $request->input('initData');

        $data = TelegramService::validateInitData($initData, env('TELEGRAM_BOT_TOKEN'));
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