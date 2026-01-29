<?php
namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Services\ClickService;
use Illuminate\Http\Request;

class ClickController extends Controller
{
    private $clickService;

    public function __construct(ClickService $clickService)
    {
        $this->clickService = $clickService;
    }

    public function prepare(Request $request)
    {
        return response()->json($this->clickService->prepare($request));
    }

    public function complete(Request $request)
    {
        return response()->json($this->clickService->complete($request));
    }
}