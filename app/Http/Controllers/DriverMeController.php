<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DriverMeController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        return $this->response([
            'data' => ""
        ]);
    }
}
