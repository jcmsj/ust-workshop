<?php

namespace App\Livewire\Forms;

use App\Models\KanBoard;
use Livewire\Attributes\Validate;
use Livewire\Form;

class KanBoardForm extends Form
{
    public ?KanBoard $kanBoard = null;

    #[Validate('required|min:3|max:255')]
    public $name = '';

    #[Validate('nullable|max:1000')]
    public $description = '';
    
    public function setKanBoard(KanBoard $kanBoard)
    {
        $this->kanBoard = $kanBoard;
        $this->name = $kanBoard->name;
        $this->description = $kanBoard->description;
    }

    public function store()
    {
        $this->validate();
        
        $board = KanBoard::create([
            'name' => $this->name,
            'description' => $this->description,
            'user_id' => auth()->id(),
        ]);
        
        $this->reset();
        
        return $board;
    }
    
    public function update()
    {
        $this->validate();
        
        $this->kanBoard->update([
            'name' => $this->name,
            'description' => $this->description,
        ]);
        
        $this->reset();
        
        return $this->kanBoard;
    }
}
