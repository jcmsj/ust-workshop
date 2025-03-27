<div class="">
  <div class="max-w-xl">
    <form wire:submit='create' class='w-full xl:w-max overflow-y-auto xl:min-h-[90dvh] max-h-[70dvh] '>
      {{$this->form}}
    </form>
  </div>
  <x-filament-actions::modals />
</div>
