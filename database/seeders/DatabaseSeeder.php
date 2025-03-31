<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Reserve;
use App\Models\Transaction;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run user seeder first
        $this->call([
            UserSeeder::class,
            LeadSeeder::class,
            ReserveSeeder::class,
            ReserveRequestSeeder::class,
            LeadAssignmentSeeder::class,
            NotesSeeder::class,
            KanbanSeeder::class,
        ]);
    }
}
