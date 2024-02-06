<?php

namespace Database\Seeders;

use App\Models\BlogLocalExperience;
use Illuminate\Database\Seeder;

class BlogLocalExperienceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BlogLocalExperience::create([
            'author_id' => 1,
            'title' => 'test',
            'picture' => 'test',
            'content' => 'test',
            'tour_id' => null,
            'created_date' => now()
        ]);
        BlogLocalExperience::create([
            'author_id' => 2,
            'title' => 'test',
            'picture' => 'test',
            'content' => 'test',
            'tour_id' => null,
            'created_date' => now()
        ]);
        BlogLocalExperience::create([
            'author_id' => 1,
            'title' => 'test',
            'picture' => 'test',
            'content' => 'test',
            'tour_id' => 1,
            'created_date' => now()
        ]);
        BlogLocalExperience::create([
            'author_id' => 2,
            'title' => 'test',
            'picture' => 'test',
            'content' => 'test',
            'tour_id' => 1,
            'created_date' => now()
        ]);
    }
}
