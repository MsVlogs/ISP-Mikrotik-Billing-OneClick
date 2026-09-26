<?php

namespace App\Jobs\Financial;

use App\Models\FinancialSnapshot;
use App\Services\Reporting\FinancialReportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecordDailyFinancials implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(FinancialReportService $service): void
    {
        $service->snapshot(now());
    }
}
