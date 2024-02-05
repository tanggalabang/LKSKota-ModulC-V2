<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Hash;
use App\Helpers\JWTAuth;
use App\Models\ReviewRating;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;


class UserTest extends TestCase
{
    use RefreshDatabase; // This trait resets the database after each test

    /**
     * register
     */

    public function testRegisterSuccess()
    {
        $response = $this->postJson('api/user/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password', // Choose a suitable password according to your validation rules
        ]);

        $response
            ->assertStatus(201)
            ->assertJson([
                'message' => 'Register success',
            ]);

        $user = User::where('email', 'test@example.com')->first();

        $this->assertNotNull($user);

        $this->assertNotNull($user->name);

        $this->assertNotNull($user->password);

        $this->assertNotNull($response['token']);
    }

    public function testRegisterFailedInvalidData()
    {
        $response = $this->postJson('api/user/register', [
            'name' => '', // Missing name
            'email' => '', // Missing email
            'password' => '', // Missing password
        ]);

        $response->assertStatus(422); // HTTP 422 Unprocessable Entity
    }

    public function testRegisterEmailAlreadyRegistered()
    {
        // Data for the first registration attempt
        $firstUserData = [
            'name' => 'First User',
            'email' => 'duplicate@example.com',
            'password' => 'password',
        ];

        // Perform the first registration
        $this->postJson('api/user/register', $firstUserData)->assertStatus(201);

        // Data for the second registration attempt with the same email
        $secondUserData = [
            'name' => 'Second User',
            'email' => 'duplicate@example.com', // Same email as the first attempt
            'password' => 'password',
        ];

        // Perform the second registration and expect a 400 status code
        $response = $this->postJson('api/user/register', $secondUserData);

        // Verify the response status and error message
        $response->assertStatus(400)->assertJson([
            "errors" => [
                "email" => [
                    "username already registered"
                ]
            ]
        ]);
    }


    /**
     * login
     */

    public function testLoginSuccess()
    {
        // Data for the first registration attempt
        $firstUserData = [
            'name' => 'First User',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        // Perform the first registration
        $this->postJson('api/user/register', $firstUserData)->assertStatus(201);

        $response = $this->postJson('api/user/login', [
            'email' => 'test@example.com',
            'password' => 'password', // Choose a suitable password according to your validation rules
        ]);

        $response
            ->assertStatus(201)
            ->assertJson([
                'message' => 'Login success',
            ]);

        $this->assertNotNull($response['token']);
    }

    public function testLoginFailedInvalidData()
    {
        // Data for the first registration attempt
        $firstUserData = [
            'name' => 'First User',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        // Perform the first registration
        $this->postJson('api/user/register', $firstUserData)->assertStatus(201);

        $response = $this->postJson('api/user/login', [
            'email' => '',
            'password' => '',
        ]);

        $response->assertStatus(422);
    }

    public function testLoginFailedEmailWrong()
    {
        // Data for the first registration attempt
        $firstUserData = [
            'name' => 'First User',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        // Perform the first registration
        $this->postJson('api/user/register', $firstUserData)->assertStatus(201);

        $response = $this->postJson('api/user/login', [
            'email' => 'wrong@example.com',
            'password' => 'password', // Choose a suitable password according to your validation rules
        ]);

        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Email or password wrong',
            ]);
    }

    public function testLoginFailedPasswordWrong()
    {
        // Data for the first registration attempt
        $firstUserData = [
            'name' => 'First User',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        // Perform the first registration
        $this->postJson('api/user/register', $firstUserData)->assertStatus(201);

        $response = $this->postJson('api/user/login', [
            'email' => 'test@example.com',
            'password' => 'wrong', // Choose a suitable password according to your validation rules
        ]);

        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Email or password wrong',
            ]);
    }


    /**
     * get user
     */

    public function testGetSingleUserSuccess()
    {
        // Create a user
        $user = User::factory()->create([
            'name' => 'First User',
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);

        // Generate a JWT token for the created user
        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->getJson('api/user');

        // Assert the response status is 200 OK and check user details in the response
        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Get single success',
                'data' => [
                    'name' => 'First User',
                    'email' => 'test@example.com'
                ]
            ]);
    }

    public function testGetSingleUserFailedTokenInvalid()
    {
        // Create a user
        $user = User::factory()->create([
            'name' => 'First User',
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);

        // Generate a JWT token for the created user
        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->getJson('api/user');

        // Assert the response status is 401 Unauthorized
        $response
            ->assertStatus(404)
            ->assertJson([
                'error' => 'Unauthorized',
            ]);
    }

    /**
     * update user
     */

    public function testUpdateProfileSuccessDontHavePicture()
    {

        // Create a user
        $user = User::factory()->create([
            'name' => 'First User',
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);

        // Generate a JWT token for the created user
        $token = JWTAuth::createTokenJwt($user);

        $oldUser = User::where('email', 'test@example.com')->first();

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->putJson('api/user', [
            'name' => 'Update User',
            'email' => 'update@example.com',
            'password' => Hash::make('update'),
            'picture' => UploadedFile::fake()->create('document.png', 1000),
        ]);;

        $response
            ->assertStatus(200)
            ->assertJson([
                "message" => "Update success",
                "data" => [
                    'name' => 'Update User',
                    'email' => 'update@example.com',
                ]
            ]);

        $newUser = User::where('email', 'update@example.com')->first();

        self::assertNotEquals($oldUser->password, $newUser->password);


        $imageName = $newUser->picture; // Adjust based on your actual response structure
        $filePath = public_path('images/user/' . $imageName);

        // Check if the file exists
        $this->assertTrue(file_exists($filePath), "The file does not exist at path {$filePath}");
    }

    public function testUpdateProfileSuccessHavePicture()
    {

        $picture = UploadedFile::fake()->create('contoh.png', 1000);

        $imageName = 'contoh' . '.' . $picture->extension();
        $picture->move(public_path('images/user'), $imageName);

        // Create a user
        $user = User::factory()->create([
            'name' => 'First User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'picture' => $imageName
        ]);

        //  $image = $imageName;

        // Generate a JWT token for the created user
        $token = JWTAuth::createTokenJwt($user);

        $oldUser = User::where('email', 'test@example.com')->first();

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->putJson('api/user', [
            'name' => 'Update User',
            'email' => 'update@example.com',
            'password' => Hash::make('update'),
            'picture' => UploadedFile::fake()->create('document.png', 1000),
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                "message" => "Update success",
                "data" => [
                    'name' => 'Update User',
                    'email' => 'update@example.com',
                ]
            ]);

        $newUser = User::where('email', 'update@example.com')->first();

        self::assertNotEquals($oldUser->password, $newUser->password);


        $imageName = $newUser->picture; // Adjust based on your actual response structure
        $filePath = public_path('images/user/' . $imageName);

        // Check if the file exists
        $this->assertTrue(file_exists($filePath), "The file does not exist at path {$filePath}");
    }

    public function testUpdateProfileFailedDataMustBeDifferent()
    {
        // Create a user
        $user = User::factory()->create([
            'name' => 'First User',
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);

        // Generate a JWT token for the created user
        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->putJson('api/user', [
            'name' => 'First User',
            'email' => 'test@example.com',
            'password' => 'password'
        ]);;

        $response
            ->assertStatus(400)
            ->assertJson([
                "message" => "Data must be different",
            ]);
    }

    public function testUpdateProfileFailedUnvalidImage()
    {
        // Create a user
        $user = User::factory()->create([
            'name' => 'First User',
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);

        // Generate a JWT token for the created user
        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->putJson('api/user', [
            'name' => 'First User',
            'email' => 'test@example.com',
            'password' => 'password',
            'picture' => UploadedFile::fake()->create('document.pdf', 1000),
        ]);;

        $response
            ->assertStatus(422);
    }

    /**
     * destination get
     */

    public function testDestinationGet3TopSuccess()
    {

        $this->seed([\Database\Seeders\DestinationSeeder::class]);
        $this->seed([\Database\Seeders\TourSeeder::class]);

        $response = $this->getJson('api/destinations');

        // dd($response->getContent());

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Get 3 top success',
                "data" => [
                    [
                        'id' => 1,
                        'name' => 'Indonesia',
                        'picture' => 'test.jpg',
                        'tours' => 3
                    ],
                    [
                        'id' => 2,
                        'name' => 'Amerika',
                        'picture' => 'test.jpg',
                        'tours' => 2
                    ],
                    [
                        'id' => 3,
                        'name' => 'Jepang',
                        'picture' => 'test.jpg',
                        'tours' => 1,
                    ]
                ]
            ]);
    }

    public function testDestinationFailedDataNotFound()
    {
        $response = $this->getJson('api/destinations');

        // Assert the response status is 401 Unauthorized

        // dd($response->getContent());

        $response
            ->assertStatus(400)
            ->assertJson([
                'message' => 'Data not found',
            ]);
    }

    /**
     * tour get
     */

    public function testTourGet3TopSuccess()
    {
        // Create a user
        $this->seed([\Database\Seeders\UserSeeder::class]);
        $this->seed([\Database\Seeders\DestinationSeeder::class]);
        $this->seed([\Database\Seeders\TourSeeder::class]);
        $this->seed([\Database\Seeders\ReviewRatingSeeder::class]);
        $this->seed([\Database\Seeders\TourPictureSeeder::class]);

        $response = $this->getJson('api/tours');

        // dd($response->getContent());

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Get 3 top success',
                "data" => [
                    [
                        'id' => 1,
                        "picture" => "test.jpg",
                        'destination_name' => 'Indonesia',
                        'name' => 'test',
                        "review_rating" => 4.5
                    ],
                    [
                        'id' => 2,
                        "picture" => null,
                        'destination_name' => 'Indonesia',
                        'name' => 'test',
                        "review_rating" => 3.5
                    ],
                    [
                        'id' => 3,
                        "picture" => null,
                        'destination_name' => 'Indonesia',
                        'name' => 'test',
                        "review_rating" => 1.5
                    ],
                ]
            ]);
    }

    public function testTourGet3TopFailedNotFound()
    {

        $response = $this->getJson('api/tours');

        // dd($response->getContent());

        $response
            ->assertStatus(400)
            ->assertJson([
                'message' => 'Data not found',
            ]);
    }

    public function testTourGetSingleSuccess()
    {
        // Create a user

        $this->seed([\Database\Seeders\UserSeeder::class]);
        $this->seed([\Database\Seeders\DestinationSeeder::class]);
        $this->seed([\Database\Seeders\TourSeeder::class]);
        $this->seed([\Database\Seeders\ReviewRatingSeeder::class]);
        $this->seed([\Database\Seeders\TourPictureSeeder::class]);
        $this->seed([\Database\Seeders\CommentSeeder::class]);

        $response = $this->getJson('api/tours/1');

        // dd($response->getContent());

        $expectedJson = '{
            "message": "Get single success",
            "data": {
                "id": 1,
                "name": "test",
                "picture": [
                    {
                        "id": 1,
                        "tour_id": 1,
                        "picture": "test.jpg",
                        "main": 1
                    },
                    {
                        "id": 2,
                        "tour_id": 1,
                        "picture": "test.jpg",
                        "main": 0
                    },
                    {
                        "id": 3,
                        "tour_id": 1,
                        "picture": "test.jpg",
                        "main": 0
                    },
                    {
                        "id": 4,
                        "tour_id": 1,
                        "picture": "test.jpg",
                        "main": 0
                    },
                    {
                        "id": 5,
                        "tour_id": 1,
                        "picture": "test.jpg",
                        "main": 0
                    }
                ],
                "description": "test",
                "itinerary_sugesstion": "test",
                "amenities_facilities": "test",
                "maps": "test",
                "destination_name": "Indonesia",
                "review_rating": 4.5,
                "comment": [
                    {
                        "id": 1,
                        "author_id": 1,
                        "type": "tour",
                        "tour_id": 1,
                        "blog_local_experience_id": null,
                        "content": "test",
                        "created_date": "2024-02-05"
                    },
                    {
                        "id": 2,
                        "author_id": 2,
                        "type": "tour",
                        "tour_id": 1,
                        "blog_local_experience_id": null,
                        "content": "test",
                        "created_date": "2024-02-05"
                    },
                    {
                        "id": 3,
                        "author_id": 2,
                        "type": "tour",
                        "tour_id": 1,
                        "blog_local_experience_id": null,
                        "content": "test",
                        "created_date": "2024-02-05"
                    }
                ]
            }
        }';

        $response
            ->assertStatus(200)
            ->assertExactJson(json_decode($expectedJson, true));
    }

    public function testTourGetSingleFailedNotFound()
    {
        $response = $this->getJson('api/tours/999');

        // dd($response->getContent());

        $response
            ->assertStatus(400)
            ->assertJson([
                'message' => 'Data not found',
            ]);
    }

    /**
     * reviews testimonial
     */

    public function testGet3GoodTestimonialDifferentTourSuccess()
    {
        $this->seed([\Database\Seeders\UserSeeder::class]);
        $this->seed([\Database\Seeders\DestinationSeeder::class]);
        $this->seed([\Database\Seeders\TourSeeder::class]);
        $this->seed([\Database\Seeders\ReviewRatingSeeder::class]);

        $response = $this->getJson('api/reviews_testimonials');


        $expectedJson = '{
            "message": "Get 4 top success",
            "data": {
                "1": {
                    "id": 1,
                    "tour_name": "test",
                    "author_name": "Test 1",
                    "author_picture": null,
                    "content": "test",
                    "rating": 5,
                    "created_date": "2024-02-05"
                },
                "2": {
                    "id": 3,
                    "tour_name": "test",
                    "author_name": "Test 1",
                    "author_picture": null,
                    "content": "test",
                    "rating": 4,
                    "created_date": "2024-02-05"
                },
                "3": {
                    "id": 5,
                    "tour_name": "test",
                    "author_name": "Test 1",
                    "author_picture": null,
                    "content": "test",
                    "rating": 2,
                    "created_date": "2024-02-05"
                }
            }
        }';

        $response
            ->assertStatus(200)
            ->assertExactJson(json_decode($expectedJson, true));
    }

    public function testGet3GoodTestimonialFailedNotFound()
    {
        $response = $this->getJson('api/reviews_testimonials');

        $response
            ->assertStatus(400)
            ->assertJson([
                'message' => 'Data not found',
            ]);
    }

    /**
     * create review
     */
    public function testCreateReviewSuccess(){

        $this->seed([\Database\Seeders\DestinationSeeder::class]);
        $this->seed([\Database\Seeders\TourSeeder::class]);

        // Create a user
        $user = User::factory()->create([
            'name' => 'First User',
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);

        // Generate a JWT token for the created user
        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->postJson('api/reviews_testimonials',[
            'tour_id' => 1,
            'rating' => 4,
            'content' => 'test', 
        ]);

        // Assert the response status is 200 OK and check user details in the response
        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Create success',
            ]);

        $review = ReviewRating::where('content', 'test')->first();

        $this->assertNotNull($review);
    }

    public function testCreateReviewFailedInvalidData(){

        $this->seed([\Database\Seeders\DestinationSeeder::class]);
        $this->seed([\Database\Seeders\TourSeeder::class]);

        // Create a user
        $user = User::factory()->create([
            'name' => 'First User',
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);

        // Generate a JWT token for the created user
        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->postJson('api/reviews_testimonials',[
            'tour_id' => '',
            'rating' => '',
            'content' => 'test', 
        ]);

        // Assert the response status is 200 OK and check user details in the response
        $response
            ->assertStatus(422);

    }

    public function testCreateReviewFailedHaveNotBooking(){

        $this->seed([\Database\Seeders\DestinationSeeder::class]);
        $this->seed([\Database\Seeders\TourSeeder::class]);

        // Create a user
        $user = User::factory()->create([
            'name' => 'First User',
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);

        // Generate a JWT token for the created user
        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->postJson('api/reviews_testimonials',[
            'tour_id' => '',
            'rating' => '',
            'content' => 'test', 
        ]);

        // Assert the response status is 200 OK and check user details in the response
        $response
            ->assertStatus(422);

    }
}
