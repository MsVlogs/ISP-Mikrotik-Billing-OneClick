<?php

namespace Database\Factories;

use App\Models\PaymentAttempt;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentAttemptFactory extends Factory
{
    protected $model = PaymentAttempt::class;

    public function definition(): array
    {
        return [
            'provider' => $this->faker->randomElement(['bkash', 'nagad', 'rocket']),
            'provider_transaction_id' => 'TXN-' . $this->faker->unique()->numerify('##########'),
            'reference' => 'REF-' . $this->faker->numerify('####'),
            'customer_unique_id' => 'CUST-' . $this->faker->numerify('####'),
            'amount' => $this->faker->numberBetween(100, 10000),
            'currency' => 'BDT',
            'status' => $this->faker->randomElement(['pending', 'successful', 'failed']),
            'payload' => [],
        ];
    }
}
