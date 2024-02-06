<?php

namespace Database\Seeders;

use App\Models\ReviewRating;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewTestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ReviewRating::create([
            'author_id' => 1,
            'tour_id' => 1,
            'rating' => 4,
            'content' => 'test',
            'created_date'=> now(),
        ]);
        ReviewRating::create([
            'author_id' => 2,
            'tour_id' => 1,
            'rating' => 4,
            'content' => 'test',
            'created_date'=> now(),
        ]);
    }
}
