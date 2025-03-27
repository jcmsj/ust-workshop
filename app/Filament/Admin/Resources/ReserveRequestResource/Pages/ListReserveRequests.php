<?php

namespace App\Filament\Admin\Resources\ReserveRequestResource\Pages;

use App\Filament\Admin\Resources\ReserveRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReserveRequests extends ListRecords
{
    protected static string $resource = ReserveRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
