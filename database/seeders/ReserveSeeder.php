<?php

namespace Database\Seeders;

use App\Models\Reserve;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReserveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agents = User::users()->get();
        foreach ($agents as $index => $agent) {
            $count = ($index + 1) * 5; // Example logic for count
            $status = $index % 2 == 0 ? Reserve::STATUS_ACCEPTED : Reserve::STATUS_PAUSED;
            // get their reserve
            Reserve::where('user_id', $agent->id)->update([
                'count' => $count,
                'status' => $status,
            ]);
        }
    }
}
