<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    public function success($data = [], $status = 200, $message = ''): JsonResponse
    {
        return response()->json([
            'status' => "success",
            'message' => $message,
            'data' => $data
        ], $status);
    }

    public function response($data = [], int $status = 200, string $message = ''): JsonResponse
    {
        return response()->json([
            'status' => $status < 300 ? 'success' : 'error',
            'message' => $message,
            'data' => $data,
        ], $status);
    }
    
    public function error($data = [], $status = 501, $error_message = ''): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => $error_message,
            'data' => $data
        ], $status);
    }
}
