<?php

namespace App\Filament\Auth;

use Filament\Auth\Pages\Login as BaseAuthLogin;
use Illuminate\Support\Facades\Auth;

class Login extends BaseAuthLogin
{
    protected function getRedirectUrl(): string
    {
        $user = Auth::user();

        return match ($user->role) {
            'superadmin' => url('/superAdmin/dashboard'),
            'taxoparkadmin' => url('/taxoParkAdmin/dashboard'),
            default => url('/'),
        };
    }
}