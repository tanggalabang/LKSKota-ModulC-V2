<?php

namespace Database\Seeders;

use App\Models\Checkout;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CheckoutUndoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Checkout::create([
            "user_id" => 1,
            "tour_id" => 1,
            "first_name" => "John",
            "last_name" => "Doe",
            "email" => "john.doe@example.com",
            "phone" => "1234567890",
            "special_requirement" => "test",
        ]);
    }
}
