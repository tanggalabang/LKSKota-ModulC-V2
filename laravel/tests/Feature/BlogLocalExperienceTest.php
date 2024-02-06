<?php

namespace Tests\Feature;

use App\Helpers\JWTAuth;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class BlogLocalExperienceTest extends TestCase
{
    use RefreshDatabase;
    // dd($response->getContent());

    /**
     * get all blog
     */

    // 200: Success
    public function testGetAllBlogSucess()
    {
        $this->seed([
            \Database\Seeders\UserSeeder::class,
            \Database\Seeders\DestinationSeeder::class,
            \Database\Seeders\TourSeeder::class,
            \Database\Seeders\BlogLocalExperienceSeeder::class,
        ]);

        $response = $this->getJson('api/blog_local_experience');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => [
                        'title',
                        'picture',
                        'autor_name',
                        'author_picture',
                        'content',
                        'tour_id',
                        'create_date'
                    ],
                ],
            ])
            ->assertJsonMissing(['data.*.tour_id']);
    }

    // 404: Data not found
    public function testGetAllBlogNotFound()
    {
        $response = $this->getJson('api/blog_local_experience');

        $response
            ->assertStatus(404)
            ->assertJson([
                'message' => "Data not found"
            ]);
    }

    /**
     * get all local experience
     */

    // 200: Success
    public function testGetAllLocalExperienceSucess()
    {
        $this->seed([
            \Database\Seeders\UserSeeder::class,
            \Database\Seeders\DestinationSeeder::class,
            \Database\Seeders\TourSeeder::class,
            \Database\Seeders\BlogLocalExperienceSeeder::class,
        ]);

        $response = $this->getJson('api/blog_local_experience_2');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => [
                        'title',
                        'picture',
                        'autor_name',
                        'author_picture',
                        'content',
                        'tour_id',
                        'create_date'
                    ],
                ],
            ]);

        $responseData = $response->json()['data'];
        foreach ($responseData as $item) {
            $this->assertNotNull($item['tour_id']);
        }
    }

    // 404: Data not found
    public function testGetAllLocalExperienceNotFound()
    {
        $response = $this->getJson('api/blog_local_experience_2');

        $response
            ->assertStatus(404)
            ->assertJson([
                'message' => "Data not found"
            ]);
    }


    /**
     * create
     */

    // 201: Success blog
    public function testCreateBlogSuccess()
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
        ])->postJson('api/blog_local_experience', [
            'title' => 'test',
            'picture' => 'test',
            'content' => 'test',
            'tour_id' => null,
        ]);

        $response
            ->assertStatus(201)
            ->assertJson([
                'message' => 'Success blog',
            ]);
    }

    // 201: Success local experience
    public function testCreateLocalExperienceSuccess()
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
        ])->postJson('api/blog_local_experience', [
            'title' => 'test',
            'picture' => 'test',
            'content' => 'test',
            'tour_id' => 1,
        ]);

        $response
            ->assertStatus(201)
            ->assertJson([
                'message' => 'Success local experience',
            ]);
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
        ])->postJson('api/blog_local_experience', [
            ''
        ]);

        $response
            ->assertStatus(422);
    }

    // 400: User has not checked out (local experience)
    public function testCreateHasNotChecked()
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
        ])->postJson('api/blog_local_experience', [
            'title' => 'test',
            'picture' => 'test',
            'content' => 'test',
            'tour_id' => 1,
        ]);

        $response
            ->assertStatus(400)
            ->assertJson([
                'message' => 'User has not checked out (local experience)',
            ]);
    }

    /**
     * get single for update
     */

    // 200: Success
    public function testGetSingleSuccess()
    {
        $this->seed([
            \Database\Seeders\UserSeeder::class,
            \Database\Seeders\DestinationSeeder::class,
            \Database\Seeders\TourSeeder::class,
            \Database\Seeders\BlogLocalExperienceSeeder::class,
        ]);

        $response = $this->getJson('api/blog_local_experience/1');
        $response
        ->assertStatus(404)
        ->assertJson([
            'message' => "Data not found"
        ]);
    }

    // 404: Data not found
    public function testGetSingleNotFound()
    {
    }

    // 400: User not created the blog / local experience
    public function testGetSingleNotCreated()
    {
    }

    // 400: User has not checked out (local experience)
    public function testGetSingleHasNotChecked()
    {
    }

    /**
     * update single
     */

    // 200: Success
    public function testUpdateSingleSuccess()
    {
    }

    // 404: Data not found
    public function testUpdateSingleNotFound()
    {
    }

    // 400: Data must be different
    public function testUpdateSingleDataMustBeDifferent()
    {
    }

    // 400: User not created the blog / local experience
    public function testUpdateSingleNotCreated()
    {
    }

    // 400: User has not checked out (local experience)
    public function testUpdateSingleHasNotChecked()
    {
    }

    /**
     * delete single 
     */

    // 200: Success
    public function testDeleteSingleSuccess()
    {
    }

    // 404: Data not found
    public function testDeleteSingleNotFound()
    {
    }

    // 400: User not created the blog / local experience
    public function testDeleteSingleNotCreated()
    {
    }

    // 400: User has not checked out (local experience)
    public function testDeleteSingleHasNotChecked()
    {
    }
}
