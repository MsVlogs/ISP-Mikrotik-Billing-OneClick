<?php

namespace App\Filament\Widgets;

use App\Models\CollectionSummary;
use App\Models\IspExpense;
use App\Models\FinancialSnapshot;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinancialStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $today = now()->startOfDay();
        $month = now()->startOfMonth();

        $todayIncome = CollectionSummary::where('payment_status', '!=', 'failed')
            ->whereDate('collection_date', $today)
            ->sum('collection_amount');

        $monthIncome = CollectionSummary::where('payment_status', '!=', 'failed')
            ->whereDate('collection_date', '>=', $month)
            ->sum('collection_amount');

        $monthExpense = IspExpense::byMonth(now()->month, now()->year)->sum('amount');

        $snapshot = FinancialSnapshot::where('period', $month)->first();
        $profit = $snapshot ? $snapshot->profit : ($monthIncome - $monthExpense);

        return [
            Stat::make('Today Income', '৳ ' . number_format($todayIncome, 2))
                ->color('success')
                ->icon('heroicon-o-arrow-trending-up'),
            Stat::make('Month Income', '৳ ' . number_format($monthIncome, 2))
                ->color('info')
                ->icon('heroicon-o-banknotes'),
            Stat::make('Month Expense', '৳ ' . number_format($monthExpense, 2))
                ->color('danger')
                ->icon('heroicon-o-arrow-trending-down'),
            Stat::make('Month Profit', '৳ ' . number_format($profit, 2))
                ->color($profit > 0 ? 'success' : 'danger')
                ->icon('heroicon-o-chart-pie'),
        ];
    }
}
