<?php

namespace Database\Factories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'invoice_no' => 'INV-' . $this->faker->unique()->numerify('######'),
            'customer_unique_id' => 'CUST-' . $this->faker->numerify('####'),
            'billing_period' => now()->startOfMonth(),
            'subtotal' => $this->faker->numberBetween(500, 5000),
            'discount' => $this->faker->numberBetween(0, 500),
            'tax' => $this->faker->numberBetween(50, 500),
            'total' => $this->faker->numberBetween(500, 5000),
            'paid' => $this->faker->numberBetween(0, 5000),
            'status' => $this->faker->randomElement(['unpaid', 'partial', 'paid', 'overdue']),
            'due_at' => now()->addDays(7),
        ];
    }
}
