<?php

namespace App\Filament\Resources\NotesResource\Widgets;

use App\Models\Lead;
use App\Models\Notes;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

class NotesWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.resources.notes-resource.widgets.notes-widget';
    
    public ?Lead $record = null;
    public ?array $data = [];
    public ?Notes $userNote = null;

    public function mount(Lead $record): void
    {
        $this->record = $record;
        $this->userNote = $this->getUserNote();
        if ($this->userNote) {
            $this->data['content'] = $this->userNote->content;
        } else {
            $this->data['content'] = '';
        }
        
        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                RichEditor::make('content')
                    ->required()
                    ->label('Your Note'),
            ])->statePath('data');
    }

    protected function getListeners(): array
    {
        return [
            'noteUpdated' => '$refresh',
        ];
    }

    public function create(): void
    {
        // $this->form->validate(); WARNING: there's no validation here
        
        if ($this->userNote) {
            $this->userNote->update([
                'content' => $this->data['content']
            ]);
        } else {
            Notes::create([
                'content' => $this->data['content'],
                'user_id' => auth()->id(),
                'lead_id' => $this->record->id,
            ]);
        }
        
        Notification::make()
            ->success()
            ->title('Note saved successfully')
            ->send();
    }

    public function getUserNote()
    {
        return $this->record->notes()
            ->where('user_id', auth()->id())
            ->first();
    }

    public function getOtherNotes()
    {
        return $this->record->notes()
            ->where('user_id', '!=', auth()->id())
            ->latest()
            ->get();
    }

    public function deleteNote($noteId): void 
    {
        $note = Notes::find($noteId);
        if ($note && $note->canEdit()) {
            $note->delete();
        }
    }
}
