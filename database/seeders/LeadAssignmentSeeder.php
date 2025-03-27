<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LeadAssignment;
use App\Models\Lead;
use App\Models\User;

class LeadAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::users()->get();
        $insuranceQuotes = Lead::all()->shuffle();
        foreach ($insuranceQuotes as $quote) {
            $user = $users->random();
            $userLeadAssignmentCount = LeadAssignment::where('user_id', $user->id)->count();
            if ($userLeadAssignmentCount < 3) {
                LeadAssignment::factory()->createQuietly([
                    'user_id' => $user->id,
                    'lead_id' => $quote->id,
                ]);
            }
        }
    }
}
