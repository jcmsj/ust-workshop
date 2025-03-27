<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\LeadAssignment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // get successful and failed LeadAssignments
        $LeadAssignments = LeadAssignment::whereIn('status', [LeadAssignment::STATUS_SUCCESS, LeadAssignment::STATUS_FAILED])->get(['lead_id']);
        // get non-admin users
        $users = User::users()->get(['id']);
        // use lorem
        $faker = \Faker\Factory::create();
        // create notes for each insurance quote
        foreach ($LeadAssignments as $LeadAssignment) {
            $insuranceQuote = Lead::find($LeadAssignment->lead_id);
            $insuranceQuote->notes()->create([
                'content' => $faker->paragraph(),
                // pick a random user
                'user_id' => $users->random()->id,
            ]);
        }
    }
}
