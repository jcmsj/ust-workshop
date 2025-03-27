<?php

namespace Database\Seeders;

use App\Models\ArticleCategory;
use Database\Factories\UserFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
class ProductionAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        UserFactory::new()->create([
            'first_name' => 'jc',
            'last_name' => 'sanjuan',
            'email' => 'sanjuan.jeancarlo@gmail.com',
            'password' => Hash::make('We thirst for the Seven Wailings'),
            'role' => User::ROLE_ADMIN,
            'is_approved' => true,
        ]);

        // create the main and feature categories
        ArticleCategory::create([
            'name' => 'main',
            'label' => 'Main',
        ]);

        ArticleCategory::create([
            'name' => 'featured',
            'label' => 'Featured',
        ]);
    }
}
