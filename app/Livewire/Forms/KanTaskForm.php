<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\KanTask;

class KanTaskForm extends Form
{
    public $id;
    public $title = '';
    public $content = '';
    public $list_id;
    public $order;
    
    public function rules()
    {
        return [
            'title' => 'required|min:3',
            'content' => 'nullable',
            'list_id' => 'required|exists:kan_lists,id',
            'order' => 'nullable|integer',
        ];
    }
    
    public function setKantask(KanTask $task)
    {
        $this->id = $task->id;
        $this->title = $task->title;
        $this->content = $task->content;
        $this->list_id = $task->list_id;
        $this->order = $task->order;
    }
    
    public function update()
    {
        $this->validate();
        
        $task = KanTask::find($this->id);
        $oldListId = $task->list_id;
        
        // If the task is being moved to a different list
        if ($oldListId != $this->list_id) {
            // Reorder tasks in the old list to fill the gap
            KanTask::where('list_id', $oldListId)
                ->where('order', '>', $task->order)
                ->decrement('order');
                
            // Set the order to the end of the new list if not specified
            if (!$this->order) {
                $this->order = KanTask::where('list_id', $this->list_id)->max('order') + 1;
            }
        }
        
        $task->update([
            'title' => $this->title,
            'content' => $this->content,
            'list_id' => $this->list_id,
            'order' => $this->order,
        ]);
        
        return $task;
    }
    
    public function store()
    {
        $this->validate();
        
        // Create new task at the end of the list if order not specified
        if (!$this->order) {
            $this->order = KanTask::where('list_id', $this->list_id)->max('order') + 1;
        }
        
        return KanTask::create([
            'title' => $this->title,
            'content' => $this->content,
            'list_id' => $this->list_id,
            'order' => $this->order,
        ]);
    }
    
    public function updateTaskOrder($listId, $items)
    {
        foreach ($items as $item) {
            $task = KanTask::find($item['value']);
            if ($task) {
                $oldListId = $task->list_id;
                $task->update([
                    'order' => $item['order'],
                    'list_id' => $listId
                ]);
                
                // If the task was moved to a different list, reorder the old list
                if ($oldListId != $listId) {
                    $this->reorderListTasks($oldListId);
                }
            }
        }
        
        // Make sure the new list is properly ordered without gaps
        $this->reorderListTasks($listId);
    }
    
    private function reorderListTasks($listId)
    {
        $tasks = KanTask::where('list_id', $listId)
            ->orderBy('order')
            ->get();
            
        $order = 1;
        foreach ($tasks as $task) {
            $task->update(['order' => $order++]);
        }
    }
}
