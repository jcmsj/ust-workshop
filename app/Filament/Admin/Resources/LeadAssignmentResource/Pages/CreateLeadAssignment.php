<?php

namespace App\Filament\Admin\Resources\LeadAssignmentResource\Pages;

use App\Filament\Admin\Resources\LeadAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLeadAssignment extends CreateRecord
{
    protected static string $resource = LeadAssignmentResource::class;

    
    public function mount(): void
    {
        parent::mount();

        if ($leadId = request()->query('lead_id')) {
            $this->form->fill(['lead_id' => $leadId]);
        }
    }
}
