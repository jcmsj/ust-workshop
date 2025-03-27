<?php

namespace App\Livewire;

use App\Filament\Resources\LeadResource;
use App\Models\Lead;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\View\View;

class SubmitQuote extends Component implements HasForms
{
    use InteractsWithForms;
    public ?array $data = [];

    public function getRecord(): Lead
    {
        return $this->quote;
    }

    public function mount(): void
    {
        $this->form->fill();
    }
    public function form(Form $form): Form
    {
        // $form->statePath('data')->model($this->quote);
        return LeadResource::form($form)->statePath('data')->model(Lead::class);
    }

    public function render(): View
    {
        return view('livewire.submit-quote');
    }


    public function create(): void
    {
        Lead::create($this->data);

        Notification::make()
            ->success()
            ->title('Quote Submitted')
            ->send();
        $this->form->fill();
    }
}
