<?php

namespace App\Jobs\Billing;

use App\Models\Subscription;
use App\Services\Billing\BillingCycleService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateMonthlyInvoices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public function handle(BillingCycleService $billing): void
    {
        Subscription::whereIn('status',['active','past_due'])->chunkById(100, function ($subscriptions) use ($billing) { foreach ($subscriptions as $subscription) $billing->generateInvoice($subscription, now()->startOfMonth()); });
    }
}
