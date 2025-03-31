<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Lead;
use Carbon\Carbon;

class LeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leads = [];
        for ($i = 0; $i < 20; $i++) {
            $leads[] = [
                'created_at' => Carbon::now()->subDays(rand(1, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59))->subSeconds(rand(0, 59)),
            ];
        }
        Lead::factory()->createManyQuietly($leads);
    }
}
