<?php
 
namespace App\Filament\Pages;
 
use App\Filament\Widgets\LeadLineChart;
use App\Filament\Widgets\LeadProvinceBarChart;
use App\Filament\Widgets\LeadReserveStatusDistribution;
use Filament\Pages\Dashboard as BasePage;
 
class Dashboard extends BasePage
{
    protected function getHeaderWidgets(): array
    {
        return [
            LeadLineChart::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            LeadReserveStatusDistribution::class,
            LeadProvinceBarChart::class,
        ];
    }
}
