<?php

namespace App\Filament\Auth;

use Filament\Auth\Pages\Login as BaseAuthLogin;
use Illuminate\Support\Facades\Auth;

class Login extends BaseAuthLogin
{
    protected function getRedirectUrl(): string
    {
        $user = Auth::user();

        dd($user->role);

        return match ($user->role) {
            'superadmin' => route('filament.superAdmin.pages.dashboard'),
            'taxoparkadmin' => route('filament.taxoParkAdmin.pages.dashboard'),
            default => url('/'),
        };
    }
}