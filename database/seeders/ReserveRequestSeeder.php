<?php

namespace Database\Seeders;

use App\Models\ReserveRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReserveRequestSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::users()->get();
        $admin = User::admins()->first();

        foreach ($users as $user) {
            // Accepted request
            ReserveRequest::factory()->createQuietly([
                'user_id' => $user->id,
                'count' => random_int(1, 20),
                'cost_per_lead' => random_int(100, 10000) / 100, // Random price between $1 and $100
                'status' => ReserveRequest::STATUS_ACCEPTED,
                'handled_by' => $admin->id,
                'handled_at' => now()->subDays(random_int(1, 30)),
            ]);

            // Pending request
            ReserveRequest::factory()->createQuietly([
                'user_id' => $user->id,
                'count' => random_int(1, 20),
                'cost_per_lead' => random_int(100, 10000) / 100,
                'status' => ReserveRequest::STATUS_PENDING,
            ]);

            // Rejected request
            ReserveRequest::factory()->createQuietly([
                'user_id' => $user->id,
                'count' => random_int(1, 20),
                'cost_per_lead' => random_int(100, 10000) / 100,
                'status' => ReserveRequest::STATUS_REJECTED,
                'handled_by' => $admin->id,
                'handled_at' => now()->subDays(random_int(1, 30)),
            ]);

            // Cancelled request
            ReserveRequest::factory()->createQuietly([
                'user_id' => $user->id,
                'count' => random_int(1, 20),
                'cost_per_lead' => random_int(100, 10000) / 100,
                'status' => ReserveRequest::STATUS_CANCELLED,
                'handled_by' => $user->id,
                'handled_at' => now()->subDays(random_int(1, 30)),
            ]);
        }
    }
}
