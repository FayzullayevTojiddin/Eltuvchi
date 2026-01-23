<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Str;

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
        $merchantTransId = $merchantTransId = $merchantTransId ?? 'DEP_' . $userId . '_' . Str::uuid();
        
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

    public function checkSignature(array $params): bool
    {
        if (!isset(
            $params['click_trans_id'],
            $params['service_id'],
            $params['merchant_trans_id'],
            $params['amount'],
            $params['action'],
            $params['sign_time'],
            $params['sign_string']
        )) {
            return false;
        }

        $signString =
            $params['click_trans_id'] .
            $params['service_id'] .
            $this->secretKey .
            $params['merchant_trans_id'] .
            $params['amount'] .
            $params['action'] .
            $params['sign_time'];

        return md5($signString) === $params['sign_string'];
    }
}