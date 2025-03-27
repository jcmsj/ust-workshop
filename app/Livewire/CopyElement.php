<?php

namespace App\Livewire;

use Livewire\Component;

class CopyElement extends Component
{
    public $value;
    protected $content;

    public function mount($value, $slot = '')
    {
        $this->value = $value;
        $this->content = $slot;
    }

    public function render()
    {
        return <<<'HTML'
        <div x-data="{ 
            tooltipOpen: false, 
            tooltipText: 'copy',
            async copyText() {
                await navigator.clipboard.writeText(this.$wire.value)
                this.tooltipText = 'copied!';
                this.tooltipOpen = true;
                setTimeout(() => {
                    this.tooltipText = 'copy';
                    this.tooltipOpen = false;
                }, 1000);
            }
        }" 
        class="tooltip tooltip-right cursor-pointer" 
        :class="{ 'tooltip-open': tooltipOpen }" 
        :data-tip="tooltipText"
        @click="copyText">
            <div class="w-full text-start">
                {{ $value }}
            </div>
        </div>
        HTML;
    }
}
