<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 40);
            $table->string('provider_transaction_id', 120);
            $table->string('reference')->nullable()->index();
            $table->string('customer_unique_id')->nullable()->index();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('BDT');
            $table->string('status', 30)->default('pending')->index();
            $table->json('payload')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->unique(['provider', 'provider_transaction_id']);
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('customer_unique_id')->index();
            $table->unsignedBigInteger('package_id')->nullable()->index();
            $table->string('status', 30)->default('active')->index();
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable()->index();
            $table->unsignedInteger('grace_days')->default(0);
            $table->timestamp('suspended_at')->nullable();
            $table->string('suspension_reason')->nullable();
            $table->timestamps();
            $table->index(['customer_unique_id', 'status']);
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->string('customer_unique_id')->index();
            $table->unsignedBigInteger('subscription_id')->nullable()->index();
            $table->date('billing_period')->index();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('paid', 12, 2)->default(0);
            $table->string('status', 30)->default('unpaid')->index();
            $table->date('due_at')->nullable();
            $table->timestamps();
            $table->unique(['customer_unique_id', 'billing_period']);
        });

        Schema::create('financial_snapshots', function (Blueprint $table) {
            $table->id();
            $table->date('period')->unique();
            $table->decimal('income', 14, 2)->default(0);
            $table->decimal('expenses', 14, 2)->default(0);
            $table->decimal('commissions', 14, 2)->default(0);
            $table->decimal('profit', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('payment_plans', function (Blueprint $table) {
            $table->id();
            $table->string('customer_unique_id')->index();
            $table->decimal('total_amount', 12, 2);
            $table->unsignedInteger('installments');
            $table->decimal('installment_amount', 12, 2);
            $table->string('status', 30)->default('active')->index();
            $table->date('next_due_at')->nullable();
            $table->timestamps();
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('employee_no')->unique();
            $table->string('department')->nullable();
            $table->string('job_title')->nullable();
            $table->decimal('salary', 12, 2)->nullable();
            $table->date('joined_at')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();
        });

        Schema::create('channels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('stream_url');
            $table->string('category')->nullable();
            $table->string('quality')->nullable();
            $table->string('epg_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('bandwidth_usage', function (Blueprint $table) {
            $table->id();
            $table->string('customer_unique_id')->index();
            $table->unsignedBigInteger('router_id')->nullable()->index();
            $table->date('usage_date')->index();
            $table->decimal('download_gb', 12, 3)->default(0);
            $table->decimal('upload_gb', 12, 3)->default(0);
            $table->decimal('cost', 12, 2)->default(0);
            $table->timestamps();
            $table->unique(['customer_unique_id', 'usage_date']);
        });

        Schema::create('router_health_snapshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('router_id')->index();
            $table->string('status', 20)->index();
            $table->unsignedInteger('latency_ms')->nullable();
            $table->unsignedInteger('active_sessions')->nullable();
            $table->decimal('cpu_percent', 5, 2)->nullable();
            $table->decimal('memory_percent', 5, 2)->nullable();
            $table->text('error')->nullable();
            $table->timestamp('checked_at')->index();
            $table->timestamps();
        });

        Schema::create('pon_ports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('olt_id')->index();
            $table->string('port_no');
            $table->string('status', 20)->default('available')->index();
            $table->unsignedInteger('onu_capacity')->default(0);
            $table->unsignedInteger('onu_online')->default(0);
            $table->decimal('rx_power', 8, 2)->nullable();
            $table->timestamps();
            $table->unique(['olt_id', 'port_no']);
        });

        Schema::create('channel_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('monthly_price', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('channel_package_channel', function (Blueprint $table) {
            $table->foreignId('channel_package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('channel_id')->constrained()->cascadeOnDelete();
            $table->primary(['channel_package_id', 'channel_id']);
        });
    }

    public function down(): void
    {
        foreach (['channel_package_channel','channel_packages','pon_ports','router_health_snapshots','bandwidth_usage','channels','employees','payment_plans','financial_snapshots','invoices','subscriptions','payment_attempts'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
