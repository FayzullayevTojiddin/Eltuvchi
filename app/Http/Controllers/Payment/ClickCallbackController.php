<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\DepositTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;

class ClickCallbackController extends Controller
{
    public function prepare(Request $request)
    {
        $merchantTransId = $request->merchant_trans_id;
        
        $transaction = DepositTransaction::where('merchant_trans_id', $merchantTransId)
            ->where('status', 'pending')
            ->first();
        
        if (!$transaction) {
            return response()->json([
                'error' => -5,
                'error_note' => 'Transaction not found'
            ]);
        }

        if ($transaction->amount != $request->amount) {
            return response()->json([
                'error' => -2,
                'error_note' => 'Invalid amount'
            ]);
        }

        return response()->json([
            'click_trans_id' => $request->click_trans_id,
            'merchant_trans_id' => $merchantTransId,
            'merchant_prepare_id' => $transaction->id,
            'error' => 0,
            'error_note' => 'Success'
        ]);
    }

    public function complete(Request $request)
    {
        $merchantTransId = $request->merchant_trans_id;
        
        $transaction = DepositTransaction::where('merchant_trans_id', $merchantTransId)->first();
        
        if (!$transaction) {
            return response()->json([
                'error' => -5,
                'error_note' => 'Transaction not found'
            ]);
        }

        $user = $transaction->user;

        if ($request->error == 0) {
            $transaction->markAsSuccess($request->click_trans_id);
            
            $user->connected->addBalance($transaction->amount, "Click to'lov #{$request->click_trans_id}");
            
            $this->notifyUser($user, $transaction->amount);
        } else {
            $transaction->markAsFailed($request->error_note ?? 'Payment failed');
        }

        return response()->json([
            'click_trans_id' => $request->click_trans_id,
            'merchant_trans_id' => $merchantTransId,
            'merchant_confirm_id' => $transaction->id,
            'error' => 0,
            'error_note' => 'Success'
        ]);
    }

    private function notifyUser($user, $amount)
    {
        try {
            $telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
            
            $text = "✅ To'lov muvaffaqiyatli amalga oshirildi!\n\n";
            $text .= "💰 Summa: " . number_format($amount, 0, '.', ' ') . " so'm\n";
            $text .= "💳 Joriy balans: " . number_format($user->connected->balance, 0, '.', ' ') . " so'm";
            
            $telegram->sendMessage([
                'chat_id' => $user->telegram_id,
                'text' => $text
            ]);
        } catch (\Exception $e) {
            Log::error('Telegram notification error: ' . $e->getMessage());
        }
    }
}