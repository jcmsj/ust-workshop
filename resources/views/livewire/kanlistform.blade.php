<?php

use function Livewire\Volt\{state, form, mount};
use App\Models\KanList;
use App\Livewire\Forms\KanListForm;

state([
    'list' => null,
    'isEditing' => false,
]);

form(KanListForm::class);

mount(function ($list = null, $boardId = null) {
    if ($list) {
        $this->list = $list;
        $this->form->setKanList(KanList::find($list['id']));
        $this->isEditing = true;
    } else if ($boardId) {
        // Creating a new list
        $this->isEditing = false;
        $this->form->board_id = $boardId;
        // Count existing lists to determine order
        $this->form->order = KanList::where('board_id', $boardId)->count();
    }
});

$save = function () {
    if ($this->isEditing) {
        $list = $this->form->update();
        $this->dispatch('list-updated', list: $list->toArray());
    } else {
        $list = $this->form->store();
        $this->dispatch('list-created', list: $list->toArray());
    }
    $this->dispatch('close-list-modal');
};

$cancel = function () {
    $this->dispatch('close-list-modal');
};
?>

<div>
    <form wire:submit.prevent="save" class="space-y-4">
        <div class="form-control">
            <label class="label">
                <span class="label-text">List Title</span>
            </label>
            <input wire:model="form.title" type="text" class="input input-bordered" required />
            @error('form.title') <span class="text-error text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="form-control">
            <label class="label">
                <span class="label-text">Marker Color</span>
            </label>
            <input wire:model="form.marker_color" type="color" class="w-full h-10 cursor-pointer rounded"/>
            @error('form.marker_color') <span class="text-error text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="flex justify-end gap-2">
            <button type="button" class="btn btn-ghost" wire:click="cancel">Cancel</button>
            <button type="submit" class="btn btn-primary">
                {{ $isEditing ? 'Update' : 'Create' }} List
            </button>
        </div>
    </form>
</div>
