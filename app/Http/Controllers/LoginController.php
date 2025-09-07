<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->role === 'superadmin') {
                return redirect()->route('filament.superAdmin.pages.dashboard');
            }

            if ($user->role === 'taxoparkadmin') {
                return url('https://youtube.com');
            }

            Auth::logout();
            return back()->withErrors([
                'email' => 'Sizning rolingiz uchun panel mavjud emas.',
            ]);
        }

        return back()->withErrors([
            'email' => 'Login yoki parol noto‘g‘ri.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}