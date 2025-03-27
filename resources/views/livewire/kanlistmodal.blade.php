<?php

use function Livewire\Volt\{state};

state([
    'editingListId' => null,
    'lists' => [],
    'board',
]);

?>
<dialog id="list-form-modal" class="modal" open>
    <div class="modal-box">
        <h3 class="text-lg font-bold">
            {{ $editingListId ? 'Edit List' : 'Create New List' }}
        </h3>
        @if ($editingListId)
            @php
                $selectedList = collect($this->lists)->firstWhere('id', $editingListId);
            @endphp
            <livewire:kanlistform :list="$selectedList" :key="'edit-list-'.$editingListId" />
        @else
            <livewire:kanlistform :board-id="$board->id" :key="'new-list'" />
        @endif
    </div>
    <!-- closes dialog when clicked outside -->
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>
