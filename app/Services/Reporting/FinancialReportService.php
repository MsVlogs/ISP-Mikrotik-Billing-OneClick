<?php

namespace App\Services\Reporting;

use App\Models\CollectionSummary;
use App\Models\FinancialSnapshot;
use App\Models\IspExpense;
use Illuminate\Support\Carbon;

class FinancialReportService
{
    public function snapshot(Carbon $period): FinancialSnapshot
    {
        $income = CollectionSummary::where('payment_status', '!=', 'failed')->whereMonth('collection_date',$period->month)->whereYear('collection_date',$period->year)->sum('collection_amount');
        $expenses = IspExpense::byMonth($period->month, $period->year)->sum('amount');
        return FinancialSnapshot::updateOrCreate(['period'=>$period->copy()->startOfMonth()], ['income'=>$income,'expenses'=>$expenses,'commissions'=>0,'profit'=>$income-$expenses]);
    }
}
