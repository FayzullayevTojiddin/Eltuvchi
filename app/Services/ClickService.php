<?php
namespace App\Services;

use App\Models\DepositRequest;
use Illuminate\Support\Facades\Log;

class ClickService
{
    private $merchantId;
    private $serviceId;
    private $secretKey;

    public function __construct()
    {
        $this->merchantId = env('CLICK_MERCHANT_ID');
        $this->serviceId = env('CLICK_SERVICE_ID');
        $this->secretKey = env('CLICK_SECRET_KEY');
    }

    public function createInvoice(int $userId, float $amount): array
    {
        $merchantTransId = 'USER_' . $userId . '_' . time();

        DepositRequest::create([
            'user_id' => $userId,
            'amount' => $amount,
            'merchant_trans_id' => $merchantTransId,
            'status' => 'pending'
        ]);

        $params = [
            'service_id' => $this->serviceId,
            'merchant_id' => $this->merchantId,
            'amount' => $amount,
            'transaction_param' => $merchantTransId,
        ];

        $url = 'https://my.click.uz/services/pay?' . http_build_query($params);

        Log::info('Click Invoice Created', [
            'user_id' => $userId,
            'merchant_trans_id' => $merchantTransId,
            'amount' => $amount
        ]);

        return [
            'url' => $url,
            'merchant_trans_id' => $merchantTransId
        ];
    }
}