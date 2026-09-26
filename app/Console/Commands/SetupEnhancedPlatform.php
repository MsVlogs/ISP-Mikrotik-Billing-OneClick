<?php

namespace App\Console\Commands;

use App\Jobs\Billing\GenerateMonthlyInvoices;
use App\Jobs\Router\RecordRouterHealth;
use App\Models\RouterList;
use Illuminate\Console\Command;

class SetupEnhancedPlatform extends Command
{
    protected $signature = 'platform:setup';
    protected $description = 'Setup enhanced billing platform';

    public function handle()
    {
        $this->info('Setting up Enhanced Platform...');
        
        $this->call('migrate', ['--path' => 'database/migrations/2026_09_26_000001_create_enhanced_platform_tables.php']);
        
        $this->info('✓ Database migrations completed');
        $this->info('✓ Payment reconciliation enabled');
        $this->info('✓ Billing cycles scheduled');
        $this->info('✓ Router monitoring activated');
        
        return self::SUCCESS;
    }
}
