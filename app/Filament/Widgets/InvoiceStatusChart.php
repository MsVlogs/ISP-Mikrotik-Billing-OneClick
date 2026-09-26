<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class InvoiceStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Invoice Status Distribution';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $statuses = ['unpaid', 'partial', 'paid', 'overdue'];
        $data = [];
        foreach ($statuses as $status) {
            $data[] = Invoice::where('status', $status)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Invoices',
                    'data' => $data,
                    'backgroundColor' => ['#ef4444', '#eab308', '#22c55e', '#8b5cf6'],
                ]
            ],
            'labels' => array_map('ucfirst', $statuses),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
