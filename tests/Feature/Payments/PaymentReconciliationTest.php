<?php

namespace Tests\Feature\Payments;

use App\Models\PaymentAttempt;
use App\Services\Payments\PaymentReconciliationService;
use Tests\TestCase;

class PaymentReconciliationTest extends TestCase
{
    public function test_record_payment_attempt()
    {
        $service = new PaymentReconciliationService();
        $attempt = $service->receive([
            'provider' => 'bkash',
            'provider_transaction_id' => 'TXN-123456',
            'amount' => 1000,
            'currency' => 'BDT',
            'customer_unique_id' => 'CUST-001',
            'reference' => 'INV-001',
        ]);

        $this->assertInstanceOf(PaymentAttempt::class, $attempt);
        $this->assertEquals('pending', $attempt->status);
    }

    public function test_mark_payment_successful()
    {
        $attempt = PaymentAttempt::factory()->create(['status' => 'pending']);
        $service = new PaymentReconciliationService();
        $result = $service->markSuccessful($attempt);

        $this->assertEquals('successful', $result->status);
        $this->assertNotNull($result->processed_at);
    }
}
