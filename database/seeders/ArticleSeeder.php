<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use App\Models\ArticleCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create();

        // Create categories
        $mainCategory = ArticleCategory::create([
            'label' => 'Main',
            'name' => 'main',
        ]);

        $featuredCategory = ArticleCategory::create([
            'label' => 'Featured',
            'name' => 'featured',
        ]);

        Article::create([
            'title' => 'Getting Started with Laravel',
            'slug' => 'getting-started-with-laravel',
            'summary' => 'A comprehensive guide to starting your journey with Laravel framework.',
            'body' => '<p>Laravel is a powerful PHP framework...</p> <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsam nostrum accusamus molestiae aperiam quibusdam, cupiditate exercitationem tempore, hic fugiat voluptatum iste dolorem reprehenderit neque corrupti ratione quia facilis aliquid dolores suscipit inventore! Consequatur illo asperiores dolore commodi qui similique magnam blanditiis voluptatem voluptates recusandae, nemo dicta, omnis rem expedita laboriosam nostrum incidunt sint fugiat. Placeat ullam labore consectetur reprehenderit excepturi temporibus libero sint assumenda, voluptatum hic laborum. Maxime tenetur suscipit iste fuga, quasi eligendi sequi magnam quaerat odit exercitationem ipsam quo facilis quam dolor sint nesciunt totam, explicabo praesentium ad ut possimus dolores natus ab deleniti! Ipsum eos harum voluptates officia ducimus cum praesentium possimus est odio saepe consectetur voluptate dignissimos placeat quae voluptatibus, corporis velit perspiciatis voluptatum dolores blanditiis adipisci repellat! Cumque ex nostrum ducimus fugiat animi.</p>',
            'author_id' => $user->id,
            'penname' => 'Laravel Guru',
            'category_id' => $featuredCategory->id,
            'publish_status' => 'published',
            'published_at' => now(),
            'meta_title' => 'Getting Started with Laravel - Comprehensive Guide',
            'meta_description' => 'A comprehensive guide to starting your journey with Laravel framework.',
            'meta_keywords' => 'Laravel, PHP, framework, guide',
            'meta_image' => 'path/to/image1.jpg',
            'display_order' => 1,
        ]);

        Article::create([
            'title' => 'Mastering Filament Admin',
            'slug' => 'mastering-filament-admin',
            'summary' => 'Learn how to build powerful admin panels with Laravel Filament.',
            'body' => '<p>Filament is an amazing admin panel builder...
            
            </p>',
            'author_id' => $user->id,
            'category_id' => $featuredCategory->id,
            'publish_status' => 'draft',
            'published_at' => null,
            'meta_title' => 'Mastering Filament Admin - Build Powerful Admin Panels',
            'meta_description' => 'Learn how to build powerful admin panels with Laravel Filament.',
            'meta_keywords' => 'Filament, Laravel, admin panel, guide',
            'meta_image' => 'path/to/image2.jpg',
            'display_order' => 1,
        ]);

        Article::create([
            'title' => 'About Us',
            'slug' => 'about',
            'summary' => 'Learn more about our mission, vision, and services.',
            'body' => '<p>Our mission is to empower families and individuals by providing comprehensive financial protection and strategic guidance...</p>',
            'author_id' => $user->id,
            'category_id' => $mainCategory->id,
            'publish_status' => 'published',
            'published_at' => now(),
            'meta_title' => 'About Us - Our Mission and Vision',
            'meta_description' => 'Learn more about our mission, vision, and services.',
            'meta_keywords' => 'About Us, mission, vision, services',
            'meta_image' => 'path/to/image3.jpg',
            'display_order' => 1,
        ]);
    }
}

