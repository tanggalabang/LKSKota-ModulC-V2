<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Destination;

class DestinationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Destination::create([
            'name' => 'Indonesia',
            'picture' => 'test.jpg'
        ]);
        Destination::create([
            'name' => 'Amerika',
            'picture' => 'test.jpg'
        ]);
        Destination::create([
            'name' => 'Jepang',
            'picture' => 'test.jpg'
        ]);
    }
}
