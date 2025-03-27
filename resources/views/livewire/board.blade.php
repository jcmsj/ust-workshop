<?php

use function Livewire\Volt\{state, computed, mount, on};
use App\Models\KanBoard;
use App\Models\KanList;
use App\Models\KanTask;

state([
    'board' => null,
    'lists' => [],
    'tasks' => [],
    'selectedList' => null,
    'newTaskListId' => null,
    'showListModal' => false,
    'editingListId' => null,
    'importLeadListId' => null, // Add this state variable
]);

mount(function ($board) {
    $this->board = $board;
    $this->lists = $this->board->taskLists()->orderBy('order')->get()->toArray();
    $taskIds = array_column($this->lists, 'id');
    $this->tasks = KanTask::whereIn('list_id', $taskIds)->orderBy('order')->get()->toArray();
});

// Add event listener for the task-updated event
on(['task-updated' => function ($task) {
    $index = collect($this->tasks)->search(fn($item) => $item['id'] === $task['id']);
    if ($index !== false) {
        $this->tasks[$index] = $task;
    }
}]);

// Add event listener for task-created event
on(['task-created' => function ($task) {
    $this->tasks[] = $task;
    $this->newTaskListId = null;
}]);

// Add event listener for list-updated event
on(['list-updated' => function ($list) {
    $index = collect($this->lists)->search(fn($item) => $item['id'] === $list['id']);
    if ($index !== false) {
        $this->lists[$index] = $list;
    }
    $this->editingListId = null;
    $this->showListModal = false;
}]);

// Add event listener for list-created event
on(['list-created' => function ($list) {
    $this->lists[] = $list;
    $this->showListModal = false;
}]);

// Add event listener for close-list-modal
on(['close-list-modal' => function () {
    $this->showListModal = false;
    $this->editingListId = null;
}]);

// Add event listener for cancel-edit
on(['cancel-edit' => function () {
    foreach ($this->tasks as $index => $task) {
        if (isset($task['editing']) && $task['editing']) {
            $this->tasks[$index]['editing'] = false;
        }
    }
    // Also clear new task creation
    $this->newTaskListId = null;
}]);

// Add event listener for lead-selected event
on(['lead-selected' => function ($lead, $listId) {
    // Create a new task with the lead data
    $list = KanList::find($listId);
    if ($list) {
        // Get the max order for this list
        $maxOrder = KanTask::where('list_id', $listId)->max('order') ?? 0;
        
        // Create a new task with lead data
        $task = KanTask::create([
            'title' => $lead['first_name'] . ' ' . $lead['last_name'],
            'list_id' => $listId,
            'content' => 'Email: ' . $lead['email'] . 'Phone: ' . $lead['mobile_number'] . '<br>Insurance Type: ' . $lead['insurance_type'],
            'order' => $maxOrder + 1,
            'lead_id' => $lead['id'],
        ]);
        
        // Add the new task to the tasks array
        $this->tasks[] = $task->toArray();
        $this->importLeadListId = null;
    }
}]);

// Add event listener for close-lead-modal
on(['close-lead-modal' => function () {
    $this->importLeadListId = null;
}]);

function getTasksForList($listId, $tasks) {
    return collect($tasks)
        ->filter(fn($task) => $task['list_id'] === $listId)
        ->sortBy('order')
        ->values()
        ->all();
};

$addTaskList = function () {
    $this->showListModal = true;
    $this->editingListId = null;
};

$editList = function ($listId) {
    $this->editingListId = $listId;
    $this->showListModal = true;
};

$addTask = function ($listId) {
    $this->newTaskListId = $listId;
};

$updateTask = function ($taskId, $data) {
    $index = collect($this->tasks)->search(fn($item) => $item['id'] === $taskId);
    
    if ($index !== false) {
        $task = KanTask::find($taskId);
        $task->update($data);
        $this->tasks[$index] = array_merge($this->tasks[$index], $data);
    }
};

$toggleEditTask = function ($taskId) {
    $index = collect($this->tasks)->search(fn($item) => $item['id'] === $taskId);
    
    if ($index !== false) {
        // Toggle an editing state for this task
        $this->tasks[$index]['editing'] = $this->tasks[$index]['editing'] ?? false;
        $this->tasks[$index]['editing'] = !$this->tasks[$index]['editing'];
    }
};

// New functions for sortable functionality
$updateListOrder = function ($listOrder) {
    // Update the order of lists in database
    // data: {order:int, value: list id}
    foreach ($listOrder as $index => $data) {
        $list = KanList::find($data['value']);
        if ($list) {
            $list->update(['order' => $data['order']]);
        }
    }
    
    // Refresh lists array with new order
    $this->lists = $this->board->taskLists()->orderBy('order')->get()->toArray();
};

$updateTaskOrder = function ($taskData) {
    // Process tasks moving between lists
    // taskData structure: {listId: {order: int, value: listId, items: [{order: int, value: taskId}, ...]}}
    foreach ($taskData as $_index => $listData) {
        if (isset($listData['items'])) {
            // Update each task's list_id and order
            foreach ($listData['items'] as $order => $taskItem) {
                $task = KanTask::find($taskItem['value']);
                if ($task) {
                    $task->update([
                        'list_id' => $listData['value'],
                        'order' => $taskItem['order'],
                    ]);
                    
                    // Also update the task in our local tasks array
                    $index = collect($this->tasks)->search(fn($item) => $item['id'] === $task->id);
                    if ($index !== false) {
                        $this->tasks[$index]['list_id'] = $listData['value'];
                        $this->tasks[$index]['order'] = $taskItem['order'];
                    }
                }
            }
        }
    }
    
    // Refresh tasks after reordering
    $taskIds = array_column($this->lists, 'id');
    $this->tasks = KanTask::whereIn('list_id', $taskIds)->orderBy('order')->get()->toArray();
};

$importLeadForList = function ($listId) {
    $this->importLeadListId = $listId;
};
?>

<div class="overflow-x-auto">
    <div class="flex gap-4 p-4 w-[calc(100vw-1rem)]" wire:sortable="updateListOrder" wire:sortable-group="updateTaskOrder">
        @foreach ($this->lists as $list)
        <div class="card bg-base-200 shadow-lg max-h-[80vh]" wire:key="list-{{ $list['id'] }}" wire:sortable.item="{{ $list['id'] }}">
            <div class="card-body p-4 h-full flex flex-col gap-y-4">
                <!-- List Header -->
                <div class="card-title flex justify-between items-center" wire:sortable.handle>
                    <!-- Marker color -->
                    <div class="avatar">
                        <div class="w-4 rounded-full ring ring-offset-2 ring-offset-base-100"
                            style="--tw-ring-color: {{ $list['marker_color'] }}; background-color: {{ $list['marker_color'] }}">
                        </div>
                    </div>
                    <h2 class="text-lg font-semibold cursor-pointer w-full px-3 py-2">
                        {{ $list['title'] }} 
                    </h2>
                    <div class="flex gap-1">
                        {{-- count items --}}
                        <span class="badge badge-primary m-auto">{{ count(getTasksForList($list['id'], $this->tasks)) }}</span>
                        {{-- edit list --}}
                        <button class="btn btn-square btn-ghost text-xl" wire:click="editList('{{ $list['id'] }}')">
                            <x-heroicon-o-ellipsis-vertical class="w-6 h-8" />
                        </button>
                    </div>
                </div>

                <!-- Tasks Container -->
                <div class="flex-1 flex flex-col gap-2 relative w-full overflow-y-auto">
                    <div class="space-y-2 w-[22rem]" wire:sortable-group.item-group="{{ $list['id'] }}">
                        @forelse (getTasksForList($list['id'], $this->tasks) as $index => $task)
                        <div wire:key="task-{{ $task['id'] }}" wire:sortable-group.item="{{ $task['id'] }}">
                            <div class="card card-bordered bg-base-100 z-8"  wire:sortable-group.handle>
                                <div class="card-body p-2 w-full" >
                                    @if (isset($task['editing']) && $task['editing'])
                                    <!-- Edit Mode -->
                                    <livewire:kantaskform :task="$task" :key="$task['id']"
                                        @cancelEdit="toggleEditTask('{{ $task['id'] }}')" />
                                    @else
                                    <!-- View Mode -->
                                    <div class="card-title">
                                        <h2>{{ $task['title'] }}</h2>
                                    </div>
                                    <div class="h-full w-full">{!! $task['content'] !!}</div>
                                    <div class="card-actions justify-between mt-1 items-center">
                                        {{-- add date information --}}
                                
                                        <span class="text-xs opacity-60">{{ \Carbon\Carbon::parse($task['updated_at'])->diffForHumans() }}</span>
                                        <span class="">
                                            {{-- for spacing --}}
                                        </span>
                                        <button class="btn btn-ghost btn-sm"
                                            wire:click.stop="toggleEditTask('{{ $task['id'] }}')"
                                            wire:ignore.self>
                                            <x-heroicon-o-pencil class="w-4 h-4" />
                                            Edit
                                        </button>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="card card-body bg-base-200 text-base-content text-center" wire:key="no-tasks">
                            No tasks yet
                        </div>
                        @endforelse

                        <!-- New Task Form -->
                        @if ($newTaskListId === $list['id'])
                        <div class="card card-bordered bg-base-100 z-8">
                            <div class="card-body p-2 w-full">
                                <livewire:kantaskform :list-id="$list['id']" :key="'new-task-'.$list['id']" />
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Add Task Button -->
                <div class="card-actions">
                    <div class="flex w-full gap-2">
                        <button class="btn btn-ghost flex-1 justify-start gap-2"
                            wire:click="addTask('{{ $list['id'] }}')">
                            <x-heroicon-o-plus-circle class="w-8 h-8" />
            
                            Add a task
                        </button>
                        
                        <button class="btn btn-ghost flex-1 justify-start gap-2"
                            wire:click="importLeadForList('{{ $list['id'] }}')">
                            {{-- add a heroicon for imports --}}
                            <x-heroicon-o-cloud-arrow-up class="w-6 h-6" />
                            Import Lead
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        <div class="w-max h-auto sticky">
            <!-- Add List Button -->
            <button class="btn btn-ghost justify-start gap-2 rounded-box border-2 border-dashed text-start"
                wire:click="addTaskList">
                <x-heroicon-o-plus-circle class="w-8 h-8" />
                new list
            </button>
        </div>
    </div>

    <!-- List Form Modal -->
    @if ($showListModal)
    <livewire:kanlistmodal :editing-list-id="$editingListId" :key="'list-form-modal'" :lists="$lists" :board="$board" />
    @endif

    <!-- Lead Selection Modal -->
    @if ($importLeadListId)
    <livewire:list-leads-modal :list-id="$importLeadListId" :key="'lead-modal-'.$importLeadListId" />
    @endif

    <style>
        /* Document body while dragging is happening */
        body.draggable--is-dragging {
            cursor: grabbing;
        }

        /* Style for task items being dragged (mirror element) */
        .draggable-mirror {
            opacity: 0.8;
            background-color: rgba(255, 255, 255, 0.95) !important;
            border: 2px solid #3b82f6 !important;
            border-radius: 1.2rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            transform: rotate(1deg) scale(1.02);
            z-index: 100;
            transition: transform 0.1s ease-in-out;
        }
        
        /* Original source element that's hidden during dragging */
        .draggable--original {
            opacity: 0.3;
        }
        
        /* Style for the placeholder where an item will be dropped */
        .draggable--over {
            background-color: rgba(59, 130, 246, 0.1) !important;
            border: 2px dashed #3b82f6 !important;
            border-radius: 1.2rem;
            box-shadow: none !important;
            transition: all 0.2s ease;
        }
        
        /* Container that's receiving draggable elements */
        .draggable-container--over {
            background-color: rgba(59, 130, 246, 0.05) !important;
            transition: background-color 0.2s ease;
            border-radius: 1.2rem;
        }
        
        /* Source container during dragging */
        .draggable-container--is-dragging {
            border: 1px dashed #ccc;
        }
        
        /* Animation for when element is placed */
        .draggable-source--placed {
            animation: placed 0.3s ease forwards;
        }
        
        @keyframes placed {
            0% { transform: scale(1.03); }
            100% { transform: scale(1); }
        }
        
        /* Ensure handles show the correct cursor */
        [wire\:sortable-group\.handle], 
        [wire\:sortable\.handle] {
            cursor: grab;
        }
        
        /* When actively dragging */
        [wire\:sortable-group\.handle]:active, 
        [wire\:sortable\.handle]:active {
            cursor: grabbing;
        }
    </style>
    
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
    // Initialize dialogs
    const dialogs = document.querySelectorAll('dialog');
    dialogs.forEach(dialog => {
        if (!dialog.open && dialog.hasAttribute('open')) {
            dialog.showModal();
        }
    });
});
</script>
