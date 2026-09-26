<?php

namespace Database\Factories;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        return [
            'customer_unique_id' => 'CUST-' . $this->faker->numerify('####'),
            'package_id' => null,
            'status' => $this->faker->randomElement(['active', 'past_due', 'suspended', 'churn']),
            'starts_at' => now(),
            'ends_at' => now()->addMonths(12),
            'grace_days' => 0,
        ];
    }
}
