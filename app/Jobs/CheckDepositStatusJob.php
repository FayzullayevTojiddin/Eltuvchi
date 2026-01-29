<?php

namespace App\Jobs;

use App\Models\DepositRequest;
use App\Services\ClickService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CheckDepositStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 11;
    public int $backoff = 30;

    public function __construct(public int $depositId)
    {
    }

    public function handle(ClickService $clickService)
    {
        $deposit = DepositRequest::find($this->depositId);

        if (! $deposit || $deposit->status !== 'pending') {
            return;
        }

        $result = $clickService->checkPayment($deposit->merchant_trans_id);

        if ($result['paid'] === true) {

            DB::transaction(function () use ($deposit) {
                $deposit->update(['status' => 'paid']);

                $deposit->user->increment('balance', $deposit->amount);
            });

            return;
        }

        throw new \Exception('Payment not completed yet');
    }
}