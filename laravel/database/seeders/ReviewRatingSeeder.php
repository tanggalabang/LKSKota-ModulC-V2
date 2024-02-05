<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\ReviewRating;

class ReviewRatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ReviewRating::create([
            'author_id'=> 1,
            'tour_id'=> 1,
            'rating'=> 5,
            'content'=> 'test',
            'created_date'=> now(),
        ]);
        ReviewRating::create([
            'author_id'=> 2,
            'tour_id'=> 1,
            'rating'=> 4,
            'content'=> 'test',
            'created_date'=> now(),
        ]);
        ReviewRating::create([
            'author_id'=> 1,
            'tour_id'=> 2,
            'rating'=> 4,
            'content'=> 'test',
            'created_date'=> now(),
        ]);
        ReviewRating::create([
            'author_id'=> 2,
            'tour_id'=> 2,
            'rating'=> 3,
            'content'=> 'test',
            'created_date'=> now(),
        ]);
        ReviewRating::create([
            'author_id'=> 1,
            'tour_id'=> 3,
            'rating'=> 2,
            'content'=> 'test',
            'created_date'=> now(),
        ]);
        ReviewRating::create([
            'author_id'=> 2,
            'tour_id'=> 3,
            'rating'=> 1,
            'content'=> 'test',
            'created_date'=> now(),
        ]);
    }
}
