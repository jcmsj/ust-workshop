<?php

namespace App\Filament\Resources\ReserveRequestResource\Pages;

use App\Filament\Resources\ReserveRequestResource;
use App\Models\ReserveRequest;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateReserveRequest extends CreateRecord
{
    protected static string $resource = ReserveRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!auth()->user()->is_admin) {
            $data['user_id'] = auth()->id();
        }

        return $data;
    }

    protected function authorizeAccess(): void
    {
        parent::authorizeAccess();

        if (!auth()->user()->is_admin && ReserveRequest::hasPendingRequest(auth()->id())) {
            $this->notify('warning', 'You already have a pending request.');
            $this->redirect($this->getResource()::getUrl('index'));
        }
    }
}
