<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return [
            'user' => $user,
//            'details' => $user->connected
        ];
    }
}
