<?php

namespace App\Services\Reporting;

use App\Models\CollectionSummary;
use App\Models\IspExpense;
use App\Models\ResellerWalletTransaction;
use Illuminate\Support\Carbon;

class ResellerCommissionService
{
    public function calculateCommission(int $resellerId, Carbon $period): array
    {
        $sales = CollectionSummary::where('payment_status', '!=', 'failed')
            ->whereMonth('collection_date', $period->month)
            ->whereYear('collection_date', $period->year)
            ->sum('collection_amount');

        $payouts = IspExpense::where('linked_reseller_id', $resellerId)
            ->byMonth($period->month, $period->year)
            ->sum('amount');

        $balance = ResellerWalletTransaction::where('reseller_id', $resellerId)->sum('amount');

        return [
            'sales' => $sales,
            'payouts' => $payouts,
            'balance' => $balance,
            'period' => $period->format('Y-m'),
        ];
    }
}
