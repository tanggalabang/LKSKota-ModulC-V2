<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Comment;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Comment::create([
            'author_id'=> 1,
            'type'=> 'tour',
            'tour_id'=> 1,
            'blog_local_experience_id'=> null,
            'content'=> 'test',
            'created_date'=> now(),
        ]);
        Comment::create([
            'author_id'=> 2,
            'type'=> 'tour',
            'tour_id'=> 1,
            'blog_local_experience_id'=> null,
            'content'=> 'test',
            'created_date'=> now(),
        ]);
        Comment::create([
            'author_id'=> 2,
            'type'=> 'tour',
            'tour_id'=> 1,
            'blog_local_experience_id'=> null,
            'content'=> 'test',
            'created_date'=> now(),
        ]);
    }
}
