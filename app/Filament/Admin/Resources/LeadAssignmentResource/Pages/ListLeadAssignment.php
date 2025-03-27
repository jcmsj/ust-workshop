<?php

namespace App\Filament\Admin\Resources\LeadAssignmentResource\Pages;

use App\Filament\Admin\Resources\LeadAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeadAssignment extends ListRecords
{
    protected static string $resource = LeadAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
