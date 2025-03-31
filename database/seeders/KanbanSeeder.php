<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\KanBoard;
use App\Models\KanList;
use App\Models\KanTask;

class KanbanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', '01@ustworkshop.com')->first();
        
        if (!$user) {
            $this->command->error('User with email 01@ustworkshop.com not found. Kanban data not seeded.');
            return;
        }

        // Create two boards for the user
        $boards = [
            [
                'name' => 'Project Management',
                'description' => 'Track project tasks and progress',
                'user_id' => $user->id,
            ],
            [
                'name' => 'Personal Tasks',
                'description' => 'Personal to-do list and reminders',
                'user_id' => $user->id,
            ],
        ];

        foreach ($boards as $boardData) {
            $board = KanBoard::create($boardData);

            // Create 3 lists for each board
            $lists = [
                [
                    'title' => 'To Do',
                    'order' => 1,
                    'marker_color' => '#FF5733',
                    'board_id' => $board->id,
                ],
                [
                    'title' => 'In Progress',
                    'order' => 2,
                    'marker_color' => '#33A8FF',
                    'board_id' => $board->id,
                ],
                [
                    'title' => 'Completed',
                    'order' => 3,
                    'marker_color' => '#33FF57',
                    'board_id' => $board->id,
                ],
            ];

            foreach ($lists as $listData) {
                $list = KanList::create($listData);

                $taskCount = rand(3, 8);
                $tasks = [];

                for ($i = 1; $i <= $taskCount; $i++) {
                    $tasks[] = [
                        'title' => "Task {$i} for {$list->title}",
                        'list_id' => $list->id,
                        'content' => "This is a sample task content for task {$i} in the {$list->title} list.",
                        'order' => $i,
                    ];
                }

                foreach ($tasks as $taskData) {
                    KanTask::create($taskData);
                }
            }
        }

        $this->command->info('Kanban data seeded successfully!');
    }
}
