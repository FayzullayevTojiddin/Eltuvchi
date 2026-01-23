<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClickService
{
    private $merchantId;
    private $serviceId;
    private $secretKey;
    private $baseUrl;

    public function __construct()
    {
        $this->merchantId = env('CLICK_MERCHANT_ID');
        $this->serviceId = env('CLICK_SERVICE_ID');
        $this->secretKey = env('CLICK_SECRET_KEY');
        $this->baseUrl = env('CLICK_BASE_URL');
    }

    public function createInvoice($userId, $amount, $merchantTransId = null)
    {
        $merchantTransId = $merchantTransId ?? 'DEP_' . $userId . '_' . time();
        
        $params = [
            'service_id' => $this->serviceId,
            'merchant_id' => $this->merchantId,
            'amount' => $amount,
            'transaction_param' => $userId,
            'merchant_trans_id' => $merchantTransId,
            'merchant_prepare_url' => url('/api/click/prepare'),
            'merchant_complete_url' => url('/api/click/complete'),
        ];

        $url = $this->baseUrl . '?' . http_build_query($params);
        
        return [
            'url' => $url,
            'merchant_trans_id' => $merchantTransId
        ];
    }

    public function checkSignature($params)
    {
        $signString = implode('', [
            $params['click_trans_id'] ?? '',
            $params['service_id'] ?? '',
            $this->secretKey,
            $params['merchant_trans_id'] ?? '',
            $params['amount'] ?? '',
            $params['action'] ?? '',
            $params['sign_time'] ?? '',
        ]);

        $signKey = md5($signString);

        return $signKey === ($params['sign_string'] ?? '');
    }
}