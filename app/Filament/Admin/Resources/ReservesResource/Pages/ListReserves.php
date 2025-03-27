<?php

namespace App\Filament\Admin\Resources\ReservesResource\Pages;

use App\Filament\Admin\Resources\ReservesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReserves extends ListRecords
{
    protected static string $resource = ReservesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
