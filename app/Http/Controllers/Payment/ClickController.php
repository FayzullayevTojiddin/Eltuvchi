<?php
namespace App\Http\Controller\Payment;

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
        // Keyingi qadamda yozamiz
    }

    public function complete(Request $request)
    {
        // Keyingi qadamda yozamiz
    }
}