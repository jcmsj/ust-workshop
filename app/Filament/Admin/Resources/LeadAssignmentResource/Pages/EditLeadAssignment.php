<?php

namespace App\Filament\Admin\Resources\LeadAssignmentResource\Pages;

use App\Filament\Admin\Resources\LeadAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLeadAssignment extends EditRecord
{
    protected static string $resource = LeadAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
