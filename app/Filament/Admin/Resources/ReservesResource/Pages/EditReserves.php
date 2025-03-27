<?php

namespace App\Filament\Admin\Resources\ReservesResource\Pages;

use App\Filament\Admin\Resources\ReservesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReserves extends EditRecord
{
    protected static string $resource = ReservesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
