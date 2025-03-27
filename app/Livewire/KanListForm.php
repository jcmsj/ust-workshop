<?php

namespace App\Livewire;

use App\Livewire\Forms\KanListForm as ListForm;
use App\Models\KanList;
use Livewire\Component;

class KanListForm extends Component
{
    public ListForm $form;
    public ?KanList $list = null;
    public ?string $boardId = null;
    public bool $isEditing = false;
    
    public function mount($list = null, $boardId = null)
    {
        if ($list) {
            $this->list = $list;
            $this->form->setKanList($list);
            $this->isEditing = true;
        } else if ($boardId) {
            // Creating a new list
            $this->isEditing = false;
            $this->form->board_id = $boardId;
            // Count existing lists to determine order
            $this->form->order = KanList::where('board_id', $boardId)->count();
        }
    }
    
    public function save()
    {
        if ($this->isEditing) {
            $list = $this->form->update();
            $this->dispatch('list-updated', list: $list->toArray());
        } else {
            $list = $this->form->store();
            $this->dispatch('list-created', list: $list->toArray());
        }
        $this->dispatch('close-list-modal');
    }
    
    public function cancel()
    {
        $this->dispatch('close-list-modal');
    }
    
    public function render()
    {
        return view('livewire.kanlistform');
    }
}
