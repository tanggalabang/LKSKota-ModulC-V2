<?php

namespace Tests\Feature;

use App\Helpers\JWTAuth;
use App\Models\ReviewRating;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;



class ReviewTestimonialTest extends TestCase
{


    use RefreshDatabase;
    // dd($response->getContent());

    /**
     * get all (top 3)
     */
    // 200: Success
    public function testGetAllTop3Success()
    {
        $this->seed([
            \Database\Seeders\UserSeeder::class,
            \Database\Seeders\DestinationSeeder::class,
            \Database\Seeders\TourSeeder::class,
            \Database\Seeders\ReviewRatingSeeder::class,
        ]);

        $response = $this->getJson('api/reviews_testimonials');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['id', 'tour_name', 'author_name', 'author_picture', 'content', 'rating', 'created_date'],
                ],
            ])->assertJsonCount(3, 'data');
    }

    // 404: Data not found
    public function testGetAllTop3NotFound()
    {
        $response = $this->getJson('api/reviews_testimonials');

        $response
            ->assertStatus(404)
            ->assertJson([
                'message' => "Data not found"
            ]);
    }


    /**
     * create
     */
    // 201: Success (consider using 201 for resource creation)
    public function testCreateSuccess()
    {
        $userData = [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $user = new User($userData);
        $user->password = Hash::make($userData['password']);
        $user->save();

        $this->seed([
            \Database\Seeders\DestinationSeeder::class,
            \Database\Seeders\TourSeeder::class,
            \Database\Seeders\CheckoutSeeder::class,
        ]);

        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->postJson('api/reviews_testimonials', [
            'tour_id' => 1,
            'rating' => 4,
            'content' => 'test',
        ]);

        $response
            ->assertStatus(201)
            ->assertJson([
                'message' => 'Success',
            ]);

        $this->assertDatabaseHas('review_ratings', ['content' => 'test']);
    }

    // 422: Validation error
    public function testCreateValidationError()
    {
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
        ])->postJson('api/reviews_testimonials', [
            ''
        ]);

        $response->assertStatus(422);
    }

    // 400: User has not checked out on the tour
    public function testCreateHatNotCheckOut()
    {
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
        ])->postJson('api/reviews_testimonials', [
            'tour_id' => 1,
            'rating' => 4,
            'content' => 'test',
        ]);

        $response
            ->assertStatus(400)
            ->assertJson([
                'message' => 'You have not booked this tour',
            ]);
    }

    // 400: Status must be done
    public function testCreateStatusMustBeDone()
    {
        $userData = [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $user = new User($userData);
        $user->password = Hash::make($userData['password']);
        $user->save();

        $this->seed([
            \Database\Seeders\DestinationSeeder::class,
            \Database\Seeders\TourSeeder::class,
            \Database\Seeders\CheckoutUndoneSeeder::class,
        ]);

        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->postJson('api/reviews_testimonials', [
            'tour_id' => 1,
            'rating' => 4,
            'content' => 'test',
        ]);

        $response
            ->assertStatus(400)
            ->assertJson([
                'message' => 'Status must be done',
            ]);
    }

    /**
     * get single for update
     */
    // 200: Success
    public function testGetSingleSuccess()
    {
        $userData = [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $user = new User($userData);
        $user->password = Hash::make($userData['password']);
        $user->save();

        $this->seed([
            \Database\Seeders\UserSeeder::class,
            \Database\Seeders\DestinationSeeder::class,
            \Database\Seeders\TourSeeder::class,
            \Database\Seeders\CheckoutSeeder::class,
            // different ReviewTestimonialSeeder & ReviewRatingSeeder
            \Database\Seeders\ReviewTestimonialSeeder::class,
        ]);

        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->getJson('api/reviews_testimonials/1');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Success',
                'data' => [
                    'author_id' => 1,
                    'tour_id' => 1,
                    'rating' => 4,
                    'content' => 'test'
                ]
            ]);

        $this->assertNotNull($response['data']['created_date']);
    }

    // 404: Data not found
    public function testGetSingleNotFound()
    {
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
        ])->getJson('api/reviews_testimonials/1');

        $response
            ->assertStatus(404)
            ->assertJson([
                'message' => "Data not found"
            ]);
    }

    // 400: User has not checked out on the tour
    public function testGetSingleHasNotChecked()
    {
        $userData = [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $user = new User($userData);
        $user->password = Hash::make($userData['password']);
        $user->save();

        $this->seed([
            \Database\Seeders\UserSeeder::class,
            \Database\Seeders\DestinationSeeder::class,
            \Database\Seeders\TourSeeder::class,
            // different ReviewTestimonialSeeder & ReviewRatingSeeder
            \Database\Seeders\ReviewTestimonialSeeder::class,
        ]);

        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->getJson('api/reviews_testimonials/1');

        $response
            ->assertStatus(400)
            ->assertJson([
                'message' => 'You have not booked this tour',
            ]);
    }

    // 400: Status must be done
    public function testGetSingleStatusMustBeDone()
    {
        $userData = [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $user = new User($userData);
        $user->password = Hash::make($userData['password']);
        $user->save();

        $this->seed([
            \Database\Seeders\UserSeeder::class,
            \Database\Seeders\DestinationSeeder::class,
            \Database\Seeders\TourSeeder::class,
            \Database\Seeders\CheckoutUndoneSeeder::class,
            // different ReviewTestimonialSeeder & ReviewRatingSeeder
            \Database\Seeders\ReviewTestimonialSeeder::class,
        ]);

        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->getJson('api/reviews_testimonials/1');

        $response
            ->assertStatus(400)
            ->assertJson([
                'message' => 'Status must be done',
            ]);
    }

    // 400: User not created the review
    public function testGetSingleNotCreated()
    {
        $userData = [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $user = new User($userData);
        $user->password = Hash::make($userData['password']);
        $user->save();


        $this->seed([
            \Database\Seeders\UserSeeder::class,
            \Database\Seeders\DestinationSeeder::class,
            \Database\Seeders\TourSeeder::class,
            \Database\Seeders\CheckoutSeeder::class,
            // different ReviewTestimonialSeeder & ReviewRatingSeeder
            \Database\Seeders\ReviewTestimonialSeeder::class,
        ]);

        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->getJson('api/reviews_testimonials/2');

        // dd($response->getContent());

        $response
            ->assertStatus(400)
            ->assertJson([
                'message' => "User not created the review"
            ]);
    }

    /**
     * update single
     */
    // 200: Success
    public function testUpdateSingleSuccess()
    {
        $userData = [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $newDataReview = [
            'rating' => 1,
            'content' => 'new',
        ];

        $user = new User($userData);
        $user->password = Hash::make($userData['password']);
        $user->save();

        $this->seed([
            \Database\Seeders\UserSeeder::class,
            \Database\Seeders\DestinationSeeder::class,
            \Database\Seeders\TourSeeder::class,
            \Database\Seeders\CheckoutSeeder::class,
            // different ReviewTestimonialSeeder & ReviewRatingSeeder
            \Database\Seeders\ReviewTestimonialSeeder::class,
        ]);

        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->putJson('api/reviews_testimonials/1', $newDataReview);

        // dd($response->getContent());


        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Success',
                'data' => [
                    'author_id' => 1,
                    'tour_id' => 1,
                    'rating' => $newDataReview['rating'],
                    'content' => $newDataReview['content'],
                ]
            ]);

        $this->assertNotNull($response['data']['created_date']);
    }

    // 404: Data not found
    public function testUpdateSingleNotFound()
    {
        $userData = [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $newDataReview = [
            'rating' => 1,
            'content' => 'new',
        ];

        $user = new User($userData);
        $user->password = Hash::make($userData['password']);
        $user->save();

        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->putJson('api/reviews_testimonials/1', $newDataReview);

        $response
            ->assertStatus(404)
            ->assertJson([
                'message' => "Data not found"
            ]);
    }

    // 400: Data must be different
    public function testUpdateSingleDataMustBeDifferent()
    {
        $userData = [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $user = new User($userData);
        $user->password = Hash::make($userData['password']);
        $user->save();

        $this->seed([
            \Database\Seeders\UserSeeder::class,
            \Database\Seeders\DestinationSeeder::class,
            \Database\Seeders\TourSeeder::class,
            \Database\Seeders\CheckoutSeeder::class,
            // different ReviewTestimonialSeeder & ReviewRatingSeeder
            \Database\Seeders\ReviewTestimonialSeeder::class,
        ]);

        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->putJson('api/reviews_testimonials/1', [
            'rating' => 4,
            'content' => 'test',
        ]);

        $response
            ->assertStatus(400)
            ->assertJson([
                "message" => "Data must be different"
            ]);
    }

    // 400: User has not checked out on the tour
    public function testUpdateSingleHasNotChecked()
    {
        $userData = [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $newDataReview = [
            'rating' => 1,
            'content' => 'new',
        ];

        $user = new User($userData);
        $user->password = Hash::make($userData['password']);
        $user->save();

        $this->seed([
            \Database\Seeders\UserSeeder::class,
            \Database\Seeders\DestinationSeeder::class,
            \Database\Seeders\TourSeeder::class,
            // different ReviewTestimonialSeeder & ReviewRatingSeeder
            \Database\Seeders\ReviewTestimonialSeeder::class,
        ]);

        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->putJson('api/reviews_testimonials/1', $newDataReview);

        $response
            ->assertStatus(400)
            ->assertJson([
                'message' => 'You have not booked this tour',
            ]);
    }

    // 400: Status must be done
    public function testUpdateSingleStatusMustBeDone()
    {
        $userData = [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $newDataReview = [
            'rating' => 1,
            'content' => 'new',
        ];

        $user = new User($userData);
        $user->password = Hash::make($userData['password']);
        $user->save();

        $this->seed([
            \Database\Seeders\UserSeeder::class,
            \Database\Seeders\DestinationSeeder::class,
            \Database\Seeders\TourSeeder::class,
            \Database\Seeders\CheckoutUndoneSeeder::class,
            // different ReviewTestimonialSeeder & ReviewRatingSeeder
            \Database\Seeders\ReviewTestimonialSeeder::class,
        ]);

        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->putJson('api/reviews_testimonials/1', $newDataReview);

        $response
            ->assertStatus(400)
            ->assertJson([
                'message' => 'Status must be done',
            ]);
    }

    // 400: User not created the review
    public function testUpdateSingleNotCreated()
    {
        $userData = [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $newDataReview = [
            'rating' => 1,
            'content' => 'new',
        ];

        $user = new User($userData);
        $user->password = Hash::make($userData['password']);
        $user->save();

        $this->seed([
            \Database\Seeders\UserSeeder::class,
            \Database\Seeders\DestinationSeeder::class,
            \Database\Seeders\TourSeeder::class,
            \Database\Seeders\CheckoutUndoneSeeder::class,
            // different ReviewTestimonialSeeder & ReviewRatingSeeder
            \Database\Seeders\ReviewTestimonialSeeder::class,
        ]);

        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->putJson('api/reviews_testimonials/2', $newDataReview);

        $response
            ->assertStatus(400)
            ->assertJson([
                'message' => "User not created the review"
            ]);
    }

    /**
     * delete single
     */
    // 200: Success
    public function testDeleteSingleSuccess()
    {
        $userData = [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $user = new User($userData);
        $user->password = Hash::make($userData['password']);
        $user->save();

        $this->seed([
            \Database\Seeders\UserSeeder::class,
            \Database\Seeders\DestinationSeeder::class,
            \Database\Seeders\TourSeeder::class,
            \Database\Seeders\CheckoutSeeder::class,
            // different ReviewTestimonialSeeder & ReviewRatingSeeder
            \Database\Seeders\ReviewTestimonialSeeder::class,
        ]);

        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->deleteJson('api/reviews_testimonials/1');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Success',
            ]);

        $review = ReviewRating::where('id', 1)->first();

        $this->assertTrue(!$review);
    }

    // 404: Data not found
    public function testDeleteSingleNotFound()
    {
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
        ])->deleteJson('api/reviews_testimonials/1');

        $response
            ->assertStatus(404)
            ->assertJson([
                'message' => "Data not found"
            ]);
    }

    // 400: User has not checked out on the tour
    public function testDeleteSingleHasNotChecked()
    {
        $userData = [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $user = new User($userData);
        $user->password = Hash::make($userData['password']);
        $user->save();

        $this->seed([
            \Database\Seeders\UserSeeder::class,
            \Database\Seeders\DestinationSeeder::class,
            \Database\Seeders\TourSeeder::class,
            // different ReviewTestimonialSeeder & ReviewRatingSeeder
            \Database\Seeders\ReviewTestimonialSeeder::class,
        ]);

        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->deleteJson('api/reviews_testimonials/1');

        $response
            ->assertStatus(400)
            ->assertJson([
                'message' => 'You have not booked this tour',
            ]);
    }

    // 400: Status must be done
    public function testDeleteSingleStatusMustBeDone()
    {
        $userData = [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $user = new User($userData);
        $user->password = Hash::make($userData['password']);
        $user->save();

        $this->seed([
            \Database\Seeders\UserSeeder::class,
            \Database\Seeders\DestinationSeeder::class,
            \Database\Seeders\TourSeeder::class,
            \Database\Seeders\CheckoutUndoneSeeder::class,
            // different ReviewTestimonialSeeder & ReviewRatingSeeder
            \Database\Seeders\ReviewTestimonialSeeder::class,
        ]);

        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->deleteJson('api/reviews_testimonials/1');

        $response
            ->assertStatus(400)
            ->assertJson([
                'message' => 'Status must be done',
            ]);
    }

    // 400: User not created the review
    public function testDeleteSingleNotCreated()
    {
        $userData = [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $user = new User($userData);
        $user->password = Hash::make($userData['password']);
        $user->save();

        $this->seed([
            \Database\Seeders\UserSeeder::class,
            \Database\Seeders\DestinationSeeder::class,
            \Database\Seeders\TourSeeder::class,
            \Database\Seeders\CheckoutUndoneSeeder::class,
            // different ReviewTestimonialSeeder & ReviewRatingSeeder
            \Database\Seeders\ReviewTestimonialSeeder::class,
        ]);

        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->deleteJson('api/reviews_testimonials/2');

        $response
            ->assertStatus(400)
            ->assertJson([
                'message' => 'User not created the review',
            ]);
    
    }
}
