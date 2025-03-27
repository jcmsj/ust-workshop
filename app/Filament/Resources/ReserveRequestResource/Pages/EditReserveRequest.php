<?php

namespace App\Filament\Resources\ReserveRequestResource\Pages;

use App\Filament\Resources\ReserveRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReserveRequest extends EditRecord
{
    protected static string $resource = ReserveRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()->is_admin),
        ];
    }
}
