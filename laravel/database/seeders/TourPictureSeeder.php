<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\TourPicture;

class TourPictureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TourPicture::create([
            'tour_id'=> 1,
            'picture'=> 'test.jpg',
            'main'=> true,
        ]);
        TourPicture::create([
            'tour_id'=> 1,
            'picture'=> 'test.jpg',
        ]);
        TourPicture::create([
            'tour_id'=> 1,
            'picture'=> 'test.jpg',
        ]);
        TourPicture::create([
            'tour_id'=> 1,
            'picture'=> 'test.jpg',
        ]);
        TourPicture::create([
            'tour_id'=> 1,
            'picture'=> 'test.jpg',
        ]);
     
    }
}
