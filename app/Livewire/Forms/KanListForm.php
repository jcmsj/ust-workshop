<?php

namespace App\Livewire\Forms;

use App\Models\KanList;
use Livewire\Attributes\Validate;
use Livewire\Form;

class KanListForm extends Form
{
    public ?KanList $kanList = null;

    #[Validate('required|min:2|max:255')]
    public $title = '';

    #[Validate('required')]
    public $marker_color = 'hsl(215, 20%, 65%)';

    #[Validate('required|exists:kan_boards,id')]
    public $board_id = '';

    public $order = 0;

    public function setKanList(KanList $kanList)
    {
        $this->kanList = $kanList;
        $this->title = $kanList->title;
        $this->marker_color = $kanList->marker_color;
        $this->board_id = $kanList->board_id;
        $this->order = $kanList->order;
    }

    public function update()
    {
        $this->validate();

        $this->kanList->update([
            'title' => $this->title,
            'marker_color' => $this->marker_color,
            'order' => $this->order,
        ]);

        $this->reset(['title', 'marker_color']);
        
        return $this->kanList;
    }
    
    public function store()
    {
        $this->validate();
        
        $list = KanList::create([
            'title' => $this->title,
            'marker_color' => $this->marker_color,
            'board_id' => $this->board_id,
            'order' => $this->order,
        ]);
        
        $this->reset(['title', 'marker_color']);
        
        return $list;
    }
}
