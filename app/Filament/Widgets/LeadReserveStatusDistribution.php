<?php

namespace App\Filament\Widgets;

use App\Models\LeadAssignment;
use App\Models\Transaction;
use Filament\Widgets\ChartWidget;

class LeadReserveStatusDistribution extends ChartWidget
{
    protected static ?string $heading = 'Lead Status Distribution';

    protected function getData(): array
    {
        $data = [
            'toCall' => LeadAssignment::to_call()->count(),
            'success' => LeadAssignment::success()->count(),
            'failed' => LeadAssignment::failed()->count(),
        ];

        return [
            'datasets' => [
                [
                    'data' => array_values($data),
                    'backgroundColor' => ['#FF9F40', '#36A2EB', '#FF6384'],
                ],
            ],
            'labels' => ['To Call', 'Success', 'Failed'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
