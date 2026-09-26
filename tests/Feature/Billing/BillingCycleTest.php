<?php

namespace Tests\Feature\Billing;

use App\Models\Invoice;
use App\Models\Subscription;
use App\Models\CustomersInfo;
use App\Models\PackageList;
use App\Services\Billing\BillingCycleService;
use Tests\TestCase;

class BillingCycleTest extends TestCase
{
    public function test_generate_invoice_for_subscription()
    {
        $customer = CustomersInfo::factory()->create();
        $package = PackageList::factory()->create(['price' => 500]);
        $subscription = Subscription::factory()->create([
            'customer_unique_id' => $customer->customer_unique_id,
            'package_id' => $package->id,
            'status' => 'active',
        ]);

        $service = new BillingCycleService();
        $invoice = $service->generateInvoice($subscription, now()->startOfMonth());

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertEquals(500, $invoice->total);
        $this->assertEquals('unpaid', $invoice->status);
    }

    public function test_suspend_expired_subscriptions()
    {
        Subscription::factory()->create([
            'status' => 'active',
            'ends_at' => now()->subDays(2),
        ]);

        $service = new BillingCycleService();
        $count = $service->suspendExpired();

        $this->assertGreaterThan(0, $count);
    }
}
