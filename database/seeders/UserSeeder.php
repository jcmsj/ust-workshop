<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create 5 admin accounts
        for ($i = 1; $i <= 5; $i++) {
            User::factory()->create([
                'first_name' => "Admin",
                'last_name' => "$i",
                'email' => "admin{$i}@ustworkshop.com",
                'role' => User::ROLE_ADMIN,
                'is_approved' => true,
            ]);
        }

        $faker = Faker::create();

        // Create 10 accounts
        for ($i = 1; $i <= 10; $i++) {
            $paddedNumber = str_pad($i, 2, '0', STR_PAD_LEFT);
            User::factory()->create([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => "{$paddedNumber}@ustworkshop.com",
                'role' => User::ROLE_USER,
                'is_approved' => $i === 1 ? true : $faker->boolean(),
            ]);
        }
    }
}
