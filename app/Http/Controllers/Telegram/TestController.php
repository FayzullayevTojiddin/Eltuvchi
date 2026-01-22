<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Request;

class TestController extends Controller
{
    public function handle(Request $request)
    {
        return response()->json(['ok' => true], 200);
    }
}
