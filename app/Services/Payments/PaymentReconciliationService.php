<?php

namespace App\Services\Payments;

use App\Models\PaymentAttempt;
use Illuminate\Support\Facades\DB;

class PaymentReconciliationService
{
    public function receive(array $data): PaymentAttempt
    {
        return DB::transaction(function () use ($data) {
            return PaymentAttempt::firstOrCreate(
                ['provider' => $data['provider'], 'provider_transaction_id' => $data['provider_transaction_id']],
                ['reference'=>$data['reference'] ?? null, 'customer_unique_id'=>$data['customer_unique_id'] ?? null, 'amount'=>$data['amount'], 'currency'=>$data['currency'] ?? 'BDT', 'status'=>'pending', 'payload'=>$data['payload'] ?? null]
            );
        });
    }

    public function markSuccessful(PaymentAttempt $attempt): PaymentAttempt
    {
        $attempt->forceFill(['status'=>'successful','processed_at'=>now()])->save();
        return $attempt->refresh();
    }
}
