<?php

namespace App\Filament\Widgets;

use App\Models\InsuranceQuote;
use App\Models\Lead;
use Filament\Widgets\ChartWidget;

class LeadProvinceBarChart extends ChartWidget
{
    protected static ?string $heading = 'Leads by Province';

    protected function getData(): array
    {
        $data = Lead::select('province_territory')
            ->selectRaw('count(*) as total')
            ->groupBy('province_territory')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Leads',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => '#36A2EB',
                ],
            ],
            'labels' => $data->pluck('province_territory')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
