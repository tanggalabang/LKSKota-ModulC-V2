<?php

namespace Tests\Feature;

use App\Helpers\JWTAuth;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;
    // dd($response->getContent());

    /**
     * create
     */
    // 201: Success
    public function testCreateSuccess()
    {
        /**
         * - assertStatus
         * - assertJson : message
         * - assertDatabaseHas : stored checkouts
         * - assertDatabaseHas : stored checkout_addresses
         * - assertDatabaseHas : stored checkout_payments
         */
        $this->seed([
            \Database\Seeders\DestinationSeeder::class,
            \Database\Seeders\TourSeeder::class,
        ]);

        $userData = [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $user = new User($userData);
        $user->password = Hash::make($userData['password']);
        $user->save();

        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->postJson('api/checkout', [
            "tour_id" => 1,
            "first_name" => "John",
            "last_name" => "Doe",
            "email" => "john.doe@example.com",
            "phone" => "1234567890",
            "address" => [
                "address_2" => "test",
                "address_1" => "123 Main St",
                "city" => "Anytown",
                "province" => "Anystate",
                "postal_code" => "12345",
                "country" => "USA"
            ],
            "special_requirement" => "test",
            "payment" => [
                "payment_method" => "credit",
                "name_of_card" => "John Doe",
                "number_of_card" => "4111111111111111",
                "expiry_date" => "12/23",
                "cvv" => "123"
            ]
        ]);

        $response
            ->assertStatus(201)
            ->assertJson([
                'message' => 'Success',
            ]);

        $this->assertDatabaseHas('checkouts', ['user_id' => $user->id]);
        $this->assertDatabaseHas('checkout_addresses', ['address_1' => '123 Main St']);
        $this->assertDatabaseHas('checkout_payments', ['name_of_card' => 'John Doe']);
    }

    // 422: Validation error
    public function testCreateValidationError()
    {
        /**
         * - assertStatus
         */
        $userData = [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $user = new User($userData);
        $user->password = Hash::make($userData['password']);
        $user->save();

        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->postJson('api/checkout', [
            ""
        ]);

        $response->assertStatus(422);

    }
}
