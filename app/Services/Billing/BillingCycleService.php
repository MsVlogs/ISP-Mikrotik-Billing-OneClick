<?php

namespace App\Services\Billing;

use App\Models\Invoice;
use App\Models\Subscription;
use Illuminate\Support\Str;

class BillingCycleService
{
    public function generateInvoice(Subscription $subscription, $period): Invoice
    {
        $price = (float) optional($subscription->package)->price;
        return Invoice::firstOrCreate(
            ['customer_unique_id'=>$subscription->customer_unique_id, 'billing_period'=>$period],
            ['invoice_no'=>'INV-'.strtoupper(Str::random(10)), 'subscription_id'=>$subscription->id, 'subtotal'=>$price, 'total'=>$price, 'due_at'=>now()->addDays(7), 'status'=>'unpaid']
        );
    }

    public function suspendExpired(): int
    {
        return Subscription::whereIn('status', ['active','past_due'])->whereDate('ends_at', '<', now()->subDay())->update(['status'=>'suspended','suspended_at'=>now(),'suspension_reason'=>'Expired subscription']);
    }
}
