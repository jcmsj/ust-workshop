<?php

namespace App\Livewire;

use App\Models\LeadAssignment;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Livewire\Component;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Actions;

class LeadAssignmentStatus extends Component implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    public LeadAssignment $record;
    public array $data = [];

    public function mount(): void
    {
        $this->data = [
            'status' => $this->record->status,
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Radio::make('status')
                    ->label('Status')
                    ->options([
                        'to call' => 'To Call',
                        'success' => 'Success',
                        'failed' => 'Failed',
                    ]),

                // ->afterStateUpdated(fn() => $this->save()),
                Actions::make([
                    Action::make('update')
                        ->label('Update')
                        ->requiresConfirmation()
                        ->action(fn() => $this->save()),
                ]),
                Placeholder::make('created_at')
                    ->label('Assigned At')
                    // human readable date
                    ->content($this->record->created_at->diffForHumans())
            ])
            ->statePath('data');
    }
    public function save(): void
    {

        $this->record->status = $this->data['status'];
        $this->record->save();

        Notification::make()
            ->success()
            ->title('Status updated successfully')
            ->send();
    }

    public function render()
    {
        return view('livewire.lead-assignment-status');
    }
}
