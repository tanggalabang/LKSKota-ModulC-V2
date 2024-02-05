<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Tour;

class TourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tour::create([
            'destination_id' => 1,
            'name' => 'test',
            'description' => 'test',
            'itinerary_sugesstion' => 'test',
            'amenities_facilities' => 'test',
            'maps' => 'test'
        ]);
        Tour::create([
            'destination_id' => 1,
            'name' => 'test',
            'description' => 'test',
            'itinerary_sugesstion' => 'test',
            'amenities_facilities' => 'test',
            'maps' => 'test'
        ]);
        Tour::create([
            'destination_id' => 1,
            'name' => 'test',
            'description' => 'test',
            'itinerary_sugesstion' => 'test',
            'amenities_facilities' => 'test',
            'maps' => 'test'
        ]);
        Tour::create([
            'destination_id' => 2,
            'name' => 'test',
            'description' => 'test',
            'itinerary_sugesstion' => 'test',
            'amenities_facilities' => 'test',
            'maps' => 'test'
        ]);
        Tour::create([
            'destination_id' => 2,
            'name' => 'test',
            'description' => 'test',
            'itinerary_sugesstion' => 'test',
            'amenities_facilities' => 'test',
            'maps' => 'test'
        ]);
        Tour::create([
            'destination_id' => 3,
            'name' => 'test',
            'description' => 'test',
            'itinerary_sugesstion' => 'test',
            'amenities_facilities' => 'test',
            'maps' => 'test'
        ]);
       
       
    }
}
