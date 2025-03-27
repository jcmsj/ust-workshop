<x-filament-widgets::widget>
    <x-filament::section>
        {{ $this->form }}
        
        <x-filament::button wire:click="create" class="mt-3">
            {{ $this->userNote ? 'Update Note' : 'Add Note' }}
        </x-filament::button>

            <div class="space-y-4 mt-6">
                <h2 class="text-lg font-semibold">Other people's notes</h2>
                @foreach ($this->getOtherNotes() as $note)
                    <x-filament::card class="p-4">
                        <div class="max-w-none">
                            {!! str($note->content)->markdown() !!}
                        </div>
                        <div class="mt-2 text-sm text-gray-500">
                            {{ $note->user->name }} - {{ $note->created_at->diffForHumans() }}
                        </div>
                    </x-filament::card>
                @endforeach
            </div>
        <x-filament-actions::modals />
    </x-filament::section>
</x-filament-widgets::widget>
