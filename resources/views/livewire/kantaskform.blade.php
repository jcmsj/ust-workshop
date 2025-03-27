<?php

use function Livewire\Volt\{state, form, mount};
use App\Models\KanTask;
use App\Models\KanList;
use App\Models\KanBoard;
use App\Livewire\Forms\KanTaskForm;
state([
    'task' => null,
    'availableLists' => [],
    'availableBoards' => [],
    'selectedBoardId' => null,
    'isEditing' => false,
]);

form(KanTaskForm::class);

mount(function ($task = null, $listId = null) {
    // Load all available boards
    $this->availableBoards = KanBoard::orderBy('name')->get()->toArray();
    
    if ($task) {
        // Edit mode
        $this->task = $task;
        $this->form->setKantask(KanTask::find($task['id']));
        $this->isEditing = true;
        
        // Get the current list and its board
        $currentList = KanList::find($task['list_id']);
        if ($currentList) {
            $this->selectedBoardId = $currentList->board_id;
            
            // Load lists for the current board
            $this->loadListsForBoard($this->selectedBoardId);
        }
    } else if ($listId) {
        // Create mode
        $this->isEditing = false;
        $this->form->list_id = $listId;
        
        // Get the current list's board
        $currentList = KanList::find($listId);
        if ($currentList) {
            $this->selectedBoardId = $currentList->board_id;
            
            // Load lists for the current board
            $this->loadListsForBoard($this->selectedBoardId);
        }
    }
});

// Method to load lists for a selected board
$loadListsForBoard = function ($boardId) {
    if ($boardId) {
        $this->availableLists = KanList::where('board_id', $boardId)
            ->orderBy('order')
            ->get()
            ->toArray();
        
        // If the current list doesn't belong to the selected board, reset it
        if ($this->form->list_id && !collect($this->availableLists)->pluck('id')->contains($this->form->list_id)) {
            // Set to the first list in the new board if available
            $this->form->list_id = count($this->availableLists) > 0 ? $this->availableLists[0]['id'] : null;
        }
    } else {
        $this->availableLists = [];
    }
};

// Method to handle board selection change
$boardChanged = function ($boardId) {
    $this->selectedBoardId = $boardId;
    $this->loadListsForBoard($boardId);
};

$cancel = function () {
    $this->dispatch('cancel-edit');
};

$save = function () {
    if ($this->isEditing) {
        // Update existing task
        $this->form->update();
        
        // Fetch the fresh task data after updating
        $updatedTask = KanTask::find($this->task['id']);
        
        // Dispatch event with the updated task data
        $this->dispatch('task-updated', task: $updatedTask->toArray());
    } else {
        // Create new task
        // Determine the next order value
        $tasksCount = KanTask::where('list_id', $this->form->list_id)->count()+1;
        $this->form->order = $tasksCount;
        
        // Create the task
        $task = $this->form->store();
        
        // Dispatch event with the new task data
        $this->dispatch('task-created', task: $task->toArray());
    }
    
    // Exit edit mode
    $this->dispatch('cancel-edit');
};
?>

<form class="card-body p-2 w-full" wire:submit.prevent='save'>
    <div class="card-title">
        <input wire:model="form.title" class="input input-bordered w-full" placeholder="Task title" />
        @error('form.title') <span class="text-error text-sm">{{ $message }}</span> @enderror
    </div>
    <textarea wire:model="form.content" class="textarea textarea-bordered w-full h-24" placeholder="Task description"></textarea>
    @error('form.content') <span class="text-error text-sm">{{ $message }}</span> @enderror

    <!-- Two-step board/list selection -->
    <div class="form-control w-full mb-2">
        <!-- Board Selection Dropdown -->
        <label class="label">
            <span class="label-text">Board</span>
        </label>
        <div class="dropdown w-full mb-2">
            <div tabindex="0" role="button" 
                class="btn w-full justify-start border border-base-300 bg-base-100 text-left">
                @if($selectedBoardId)
                    @foreach ($availableBoards as $board)
                        @if($selectedBoardId === $board['id'])
                            {{ $board['name'] }}
                        @endif
                    @endforeach
                @else
                    Select a board
                @endif
            </div>
            <ul tabindex="0" class="dropdown-content z-[2] menu p-2 shadow bg-base-100 rounded-box w-full">
                @foreach ($availableBoards as $board)
                <li>
                    <a wire:click.prevent="boardChanged('{{ $board['id'] }}')" class="flex items-center">
                        {{ $board['name'] }}
                    </a>
                </li>
                @endforeach
            </ul>
        </div>

        <!-- List Selection Dropdown -->
        <label class="label">
            <span class="label-text">List</span>
        </label>
        <div class="dropdown w-full">
            <div tabindex="0" role="button"
                class="btn w-full justify-start border border-base-300 bg-base-100 text-left 
                       {{ count($availableLists) === 0 ? 'btn-disabled' : '' }}">
                @if($form->list_id)
                    @foreach ($availableLists as $list)
                        @if($form->list_id === $list['id'])
                            <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $list['marker_color'] }};"></div>
                            {{ $list['title'] }}
                        @endif
                    @endforeach
                @else
                    {{ count($availableLists) === 0 ? 'No lists available' : 'Select a list' }}
                @endif
            </div>
            <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-full">
                @foreach ($availableLists as $list)
                <li>
                    <a wire:click.prevent="$set('form.list_id', '{{ $list['id'] }}')" class="flex items-center">
                        <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $list['marker_color'] }};">
                        </div>
                        {{ $list['title'] }}
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="card-actions justify-end mt-2">
        <button class="btn btn-ghost btn-sm" wire:click='cancel' type="button">
            Cancel
        </button>
        <button class="btn btn-primary btn-sm" type="submit">
            <x-heroicon-s-check class="w-4 h-4" />
            {{ $isEditing ? 'Update' : 'Create' }}
        </button>
    </div>
</form>
