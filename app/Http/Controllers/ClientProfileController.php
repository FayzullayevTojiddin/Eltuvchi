<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientProfileController extends Controller
{
    public function me(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->client) {
            return $this->error([], 404, 'Client not found.');
        }

        return $this->success([
            'id'         => $user->id,
            'role'       => $user->role,
            'telegram_id'=> $user->telegram_id,
            'settings'   => $user->client->settings,
        ], 200, 'Client profile retrieved successfully.');
    }

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