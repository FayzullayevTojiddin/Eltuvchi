<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Str;

class ClickService
{
    private $merchantId;
    private $serviceId;

    public function __construct()
    {
        $this->merchantId = env('CLICK_MERCHANT_ID');
        $this->serviceId = env('CLICK_SERVICE_ID');
        $this->secretKey = env('CLICK_SECRET_KEY');
        $this->baseUrl = env('CLICK_BASE_URL');
    }

    public function createInvoice(int $userId, int $amount): array
    {
        $params = [
            'service_id'        => $this->serviceId,
            'merchant_id'       => $this->merchantId,
            'amount'            => $amount,
            'transaction_param' => $userId,
        ];

        $url = 'https://my.click.uz/services/pay?' . http_build_query($params);

        return [
            'url' => $url
        ];
    }
}