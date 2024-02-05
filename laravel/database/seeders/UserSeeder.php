<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Test 1',
            'email' => 'test1@example.com',
            'password' => Hash::make('password')
         ]);
         User::create([
            'name' => 'Test 2',
            'email' => 'test2@example.com',
            'password' => Hash::make('password')
         ]);
    }
}
