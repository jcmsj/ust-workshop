<?php

namespace App\Filament\Resources\ReserveRequestResource\Pages;

use App\Filament\Resources\ReserveRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReserveRequest extends ListRecords
{
    protected static string $resource = ReserveRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
