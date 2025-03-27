<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Widgets\LeadLineChart;
use App\Filament\Widgets\LeadProvinceBarChart;
use App\Filament\Widgets\LeadReserveStatusDistribution;
use Filament\Pages\Dashboard as BasePage;

class Dashboard extends BasePage
{
    protected function getHeaderWidgets(): array
    {
        return [
     
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
      
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return "Administrator";
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }
}
