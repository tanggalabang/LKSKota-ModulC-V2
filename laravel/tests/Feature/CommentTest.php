<?php

namespace Tests\Feature;

use App\Helpers\JWTAuth;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;
    // dd($response->getContent());

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
        ]);

        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->postJson('api/comment', [
            'type' => 'tour',
            'tour_id' => 1,
            'blog_local_experience_id' => null,
            'content' => 'test',
        ]);

        $response
            ->assertStatus(201)
            ->assertJson([
                'message' => 'Success',
            ]);

        $this->assertDatabaseHas('comments', ['content' => 'test']);
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

        $this->seed([
            \Database\Seeders\DestinationSeeder::class,
            \Database\Seeders\TourSeeder::class,
        ]);

        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->postJson('api/comment', [
            ''
        ]);

        $response->assertStatus(422);
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
            \Database\Seeders\CommentSeeder::class,
        ]);

        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->getJson('api/comment/1');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Success',
                'data' => [
                    'type' => 'tour',
                    'tour_id' => 1,
                    'blog_local_experience_id' => null,
                    'content' => 'test',
                ]
            ]);
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
        ])->getJson('api/comment/1');

        $response
            ->assertStatus(404)
            ->assertJson([
                'message' => "Data not found"
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
            \Database\Seeders\CommentSeeder::class,
        ]);

        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->getJson('api/comment/2');

        $response
            ->assertStatus(400)
            ->assertJson([
                'message' => "User not created the comment"
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

        $newDataComment = [
            'type' => 'tour',
            'tour_id' => 2,
            'blog_local_experience_id' => null,
            'content' => 'update test',
        ];

        $user = new User($userData);
        $user->password = Hash::make($userData['password']);
        $user->save();

        $this->seed([
            \Database\Seeders\UserSeeder::class,
            \Database\Seeders\DestinationSeeder::class,
            \Database\Seeders\TourSeeder::class,
            \Database\Seeders\CheckoutSeeder::class,
            \Database\Seeders\CommentSeeder::class,
        ]);

        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->putJson('api/comment/1', [
            'type' => $newDataComment['type'],
            'tour_id' => $newDataComment['tour_id'],
            'blog_local_experience_id' => $newDataComment['blog_local_experience_id'],
            'content' => $newDataComment['content'],
        ]);


        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Success',
                'data' => [
                    'author_id' => 1,
                    'type' => $newDataComment['type'],
                    'tour_id' => $newDataComment['tour_id'],
                    'blog_local_experience_id' => $newDataComment['blog_local_experience_id'],
                    'content' => $newDataComment['content'],
                ]
            ]);
    }

    // 404: Data not found
    public function testUpdateSingleNotFound()
    {
        $userData = [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $newDataComment = [
            'type' => 'tour',
            'tour_id' => 2,
            'blog_local_experience_id' => null,
            'content' => 'update test',
        ];

        $user = new User($userData);
        $user->password = Hash::make($userData['password']);
        $user->save();

        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->putJson('api/comment/1', [
            'type' => $newDataComment['type'],
            'tour_id' => $newDataComment['tour_id'],
            'blog_local_experience_id' => $newDataComment['blog_local_experience_id'],
            'content' => $newDataComment['content'],
        ]);


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
            \Database\Seeders\CommentSeeder::class,
        ]);

        $token = JWTAuth::createTokenJwt($user);


        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->putJson('api/comment/1', [
            'type' => 'tour',
            'tour_id' => 1,
            'blog_local_experience_id' => null,
            'content' => 'test',
        ]);

        $response
            ->assertStatus(400)
            ->assertJson([
                "message" => "Data must be different"
            ]);
    }

    // 400: User not created the comment
    public function testUpdateSingleNotCreated()
    {

        $userData = [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $newDataComment = [
            'type' => 'tour',
            'tour_id' => 2,
            'blog_local_experience_id' => null,
            'content' => 'update test',
        ];

        $user = new User($userData);
        $user->password = Hash::make($userData['password']);
        $user->save();

        $this->seed([
            \Database\Seeders\UserSeeder::class,
            \Database\Seeders\DestinationSeeder::class,
            \Database\Seeders\TourSeeder::class,
            \Database\Seeders\CheckoutSeeder::class,
            \Database\Seeders\CommentSeeder::class,
        ]);

        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->putJson('api/comment/2', [
            'type' => $newDataComment['type'],
            'tour_id' => $newDataComment['tour_id'],
            'blog_local_experience_id' => $newDataComment['blog_local_experience_id'],
            'content' => $newDataComment['content'],
        ]);


        $response
            ->assertStatus(400)
            ->assertJson([
                'message' => "User not created the comment"
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
            \Database\Seeders\CommentSeeder::class,
        ]);

        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->deleteJson('api/comment/1');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Success',
            ]);

        $coment = Comment::where('id', 1)->first();

        $this->assertTrue(!$coment);
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
        ])->deleteJson('api/comment/1');

        $response
            ->assertStatus(404)
            ->assertJson([
                'message' => "Data not found"
            ]);
    }


    // 400: User not created the comment
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
            \Database\Seeders\CheckoutSeeder::class,
            \Database\Seeders\CommentSeeder::class,
        ]);

        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->deleteJson('api/comment/2');

        $response
            ->assertStatus(400)
            ->assertJson([
                'message' => "User not created the comment"
            ]);
    }
}
