<?php

namespace App\Filament\Pages;

use App\Models\KanBoard;
use App\Livewire\Forms\KanBoardForm;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class Kanban extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-view-columns';
    protected static ?string $navigationLabel = 'Kanban Boards';
    protected static string $view = 'filament.pages.kanban';
    protected static ?string $title = '';
    
    public $activeBoardId = null;
    public $activeBoard = null;
    public ?object $boards = null;
    public KanBoardForm $boardForm;

    public function mount()
    {
        // Load all boards once
        $this->boards = KanBoard::where('user_id', Auth::id())
            ->orderBy('created_at')
            ->get();
            
        // Set the first board as active by default if one exists
        $firstBoard = $this->boards->first();
        if ($firstBoard) {
            $this->activeBoardId = $firstBoard->id;
            $this->activeBoard = $firstBoard;
        }
    }

    public function getBoards()
    {
        return $this->boards;
    }

    public function getActiveBoard()
    {
        return $this->activeBoard;
    }

    public function setActiveBoard($boardId)
    {
        $this->activeBoardId = $boardId;
        $this->activeBoard = $this->boards->firstWhere('id', $boardId);
    }

    public function makeBoard()
    {
        $board = $this->boardForm->store();
        $this->activeBoardId = $board->id;
        $this->activeBoard = $board;
        
        // Add the new board to our collection
        $this->boards->push($board);
    }
    
    public function editBoard($boardId)
    {
        $board = $this->boards->firstWhere('id', $boardId);
        if ($board && $board->user_id == Auth::id()) {
            $this->boardForm->setKanBoard($board);
            $this->dispatch('open-edit-modal');
        }
    }
    
    public function updateBoard()
    {
        $board = $this->boardForm->update();
        if ($board->id === $this->activeBoardId) {
            $this->activeBoard = $board;
        }
        
        // Update the board in our collection
        $index = $this->boards->search(function($item) use ($board) {
            return $item->id === $board->id;
        });
        
        if ($index !== false) {
            $this->boards[$index] = $board;
        }
    }
    
    #[On('open-edit-modal')]
    public function openEditModal()
    {
        $this->dispatch('js', 'document.getElementById("edit-board-modal").showModal()');
    }
}
