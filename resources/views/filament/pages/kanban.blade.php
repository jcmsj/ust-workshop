<x-filament-panels::page>
<div class="max-w-7xl mx-auto px-4">
    @if($this->getBoards()->isEmpty())
        <div class="flex flex-col items-center justify-center py-12">
            <div class="text-center">
                <h2 class="text-xl font-semibold mb-4">You don't have any boards yet</h2>
                <button onclick="document.getElementById('new-board-modal').showModal()" class="btn btn-primary">
                    Create Your First Board
                </button>
            </div>
        </div>
    @else
        <!-- Board Tabs -->
        <div class="tabs tabs-boxed mb-1 overflow-x-auto" role="tablist" >
            @foreach ($this->getBoards() as $board)
                <button 
                    wire:key="tab-{{ $board->id }}" 
                    wire:click="setActiveBoard('{{ $board->id }}')" 
                    class="tab {{ $this->activeBoardId == $board->id ? 'tab-active' : '' }}">
                    {{ $board->name }}
                </button>
            @endforeach
            <button onclick="document.getElementById('new-board-modal').showModal()" class="tab" role="tablist" >
                <x-heroicon-o-plus class="w-5 h-5 mr-2" />
            </button>
        </div>

        <!-- Active Board Content -->
        @if($this->getActiveBoard())
            {{-- <div class="mb-6">
                <h1 class="text-2xl font-bold">{{ $this->getActiveBoard()->name }}</h1>
                @if($this->getActiveBoard()->description)
                    <p class="text-sm text-gray-500 mt-2">{{ $this->getActiveBoard()->description }}</p>
                @endif
            </div> --}}
            
            <!-- Board Content - Using existing Livewire component -->
            <livewire:board :board="$this->getActiveBoard()" :key="'board-'.$this->activeBoardId" />
        @endif
    @endif

    <!-- New Board Modal -->
    <dialog id="new-board-modal" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Create New Board</h3>
            <form wire:submit='makeBoard' method="POST">
                @csrf
                <div class="form-control mt-4">
                    <label class="label">
                        <span class="label-text">Board Name</span>
                    </label>
                    <input type="text" wire:model='boardForm.name' class="input input-bordered" required />
                    @error('boardForm.name') <span class="text-error text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="form-control mt-4">
                    <label class="label">
                        <span class="label-text">Description (optional)</span>
                    </label>
                    <textarea wire:model='boardForm.description' class="textarea textarea-bordered"></textarea>
                    @error('boardForm.description') <span class="text-error text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="modal-action">
                    <button type="submit" class="btn btn-primary" onclick="document.getElementById('new-board-modal').close()">Create</button>
                    <button type="button" class="btn" onclick="document.getElementById('new-board-modal').close()">Cancel</button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <!-- Edit Board Modal -->
    <dialog id="edit-board-modal" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Edit Board</h3>
            <form wire:submit='updateBoard' method="POST">
                @csrf
                <div class="form-control mt-4">
                    <label class="label">
                        <span class="label-text">Board Name</span>
                    </label>
                    <input type="text" wire:model='boardForm.name' class="input input-bordered" required />
                    @error('boardForm.name') <span class="text-error text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="form-control mt-4">
                    <label class="label">
                        <span class="label-text">Description (optional)</span>
                    </label>
                    <textarea wire:model='boardForm.description' class="textarea textarea-bordered"></textarea>
                    @error('boardForm.description') <span class="text-error text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="modal-action">
                    <button type="submit" class="btn btn-primary" onclick="document.getElementById('edit-board-modal').close()">Update</button>
                    <button type="button" class="btn" onclick="document.getElementById('edit-board-modal').close()">Cancel</button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
</div>
@livewireScripts
<script src="https://cdn.jsdelivr.net/gh/livewire/sortable@v1.x.x/dist/livewire-sortable.js"></script>
</x-filament-panels::page>
