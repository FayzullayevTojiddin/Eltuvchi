<?php
namespace App\Services;

use App\Models\DepositRequest;
use App\Models\User;
use App\Traits\TelegramBotTrait;
use Illuminate\Support\Facades\Log;
use Exception;

class ClickService
{
    use TelegramBotTrait;

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

        return [
            'url' => $url,
            'merchant_trans_id' => $merchantTransId
        ];
    }

    public function prepare($request): array
    {
        try {
            if (!$this->checkSignature($request)) {
                return $this->error(-1, 'SIGN CHECK FAILED!');
            }

            $deposit = DepositRequest::where('merchant_trans_id', $request->merchant_trans_id)->first();

            if (!$deposit) {
                return $this->error(-5, 'User does not exist');
            }

            if ($deposit->status === 'success') {
                return $this->error(-4, 'Already paid');
            }

            if ($deposit->amount != $request->amount) {
                return $this->error(-2, 'Incorrect parameter amount');
            }

            if ($deposit->status === 'failed') {
                return $this->error(-9, 'Transaction cancelled');
            }

            $user = User::find($deposit->user_id);
            if (!$user || !$user->connected()) {
                return $this->error(-5, 'User does not exist');
            }

            return [
                'click_trans_id' => $request->click_trans_id,
                'merchant_trans_id' => $request->merchant_trans_id,
                'merchant_prepare_id' => $deposit->id,
                'error' => 0,
                'error_note' => 'Success'
            ];

        } catch (Exception $e) {
            return $this->error(-8, 'Error in request from click');
        }
    }

    public function complete($request): array
    {
        try {
            if (!$this->checkSignature($request, true)) {
                return $this->error(-1, 'SIGN CHECK FAILED!');
            }

            $deposit = DepositRequest::find($request->merchant_prepare_id);

            if (!$deposit) {
                return $this->error(-6, 'Transaction does not exist');
            }

            if ($deposit->merchant_trans_id !== $request->merchant_trans_id) {
                return $this->error(-6, 'Transaction does not exist');
            }

            if ($deposit->status === 'failed') {
                return $this->error(-9, 'Transaction cancelled');
            }

            if ($deposit->status === 'success') {
                return [
                    'click_trans_id' => $request->click_trans_id,
                    'merchant_trans_id' => $request->merchant_trans_id,
                    'merchant_confirm_id' => $deposit->id,
                    'error' => -4,
                    'error_note' => 'Already paid'
                ];
            }

            if ($request->error < 0) {
                $deposit->markAsFailed('Click error: ' . $request->error);
                return $this->error(-9, 'Transaction cancelled');
            }

            $deposit->markAsSuccess($request->click_trans_id);

            $user = User::find($deposit->user_id);
            
            if (!$user || !$user->connected()) {
                return $this->error(-7, 'Failed to update user');
            }

            try {
                $user->connected()->addBalance(
                    (int)$deposit->amount,
                    'Click to\'lov. ID: ' . $request->merchant_trans_id,
                    $user->id
                );
            } catch (Exception $e) {
                return $this->error(-7, 'Failed to update user');
            }

            $this->sendTelegramMessage($user->telegram_id, "To'lov muvaffaqiyatli tasdiqlandi. Hisobingizga $deposit->amount so'm pul o'tkazildi. ");
            

            return [
                'click_trans_id' => $request->click_trans_id,
                'merchant_trans_id' => $request->merchant_trans_id,
                'merchant_confirm_id' => $deposit->id,
                'error' => 0,
                'error_note' => 'Success'
            ];

        } catch (Exception $e) {
            return $this->error(-8, 'Error in request from click');
        }
    }

    private function checkSignature($request, $isComplete = false): bool
    {
        if ($isComplete) {
            $signString = md5(
                $request->click_trans_id .
                $request->service_id .
                $this->secretKey .
                $request->merchant_trans_id .
                $request->merchant_prepare_id .
                $request->amount .
                $request->action .
                $request->sign_time
            );
        } else {
            $signString = md5(
                $request->click_trans_id .
                $request->service_id .
                $this->secretKey .
                $request->merchant_trans_id .
                $request->amount .
                $request->action .
                $request->sign_time
            );
        }

        return $signString === $request->sign_string;
    }

    private function error(int $code, string $note): array
    {
        return [
            'error' => $code,
            'error_note' => $note
        ];
    }
}