<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TourTest extends TestCase
{

    use RefreshDatabase;
    // dd($response->getContent());

    /**
     * get all (top 3)
     */

    // 200: Success
    public function testGetAllTop3Success()
    {
        /**
         * - assertStatus
         * - assertJsonStructure
         * - assertJsonCount : sum of json value
         */
        $this->seed([
            \Database\Seeders\UserSeeder::class,
            \Database\Seeders\DestinationSeeder::class,
            \Database\Seeders\TourSeeder::class,
            \Database\Seeders\ReviewRatingSeeder::class,
            \Database\Seeders\TourPictureSeeder::class,
        ]);

        $response = $this->getJson('api/tours');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['id', 'name', 'picture', 'destination_name', 'review_rating'],
                ],
            ])->assertJsonCount(3, 'data');
    }

    // 404: Data not found
    public function testGetAllTop3DataNotFound()
    {
        /**
         * - assertStatus
         * - assertJson : message
         */
        $response = $this->getJson('api/tours');

        $response
            ->assertStatus(404)
            ->assertJson([
                'message' => "Data not found"
            ]);
    }

    /**
     * get single 
     */

    // 200: Success
    public function testGetSingleSuccess()
    {
        /**
         * - assertStatus
         * - assertJsonStructure
         * - assertJsonCount : sum of json value
         */
        $this->seed([
            \Database\Seeders\UserSeeder::class,
            \Database\Seeders\DestinationSeeder::class,
            \Database\Seeders\TourSeeder::class,
            \Database\Seeders\ReviewRatingSeeder::class,
            \Database\Seeders\TourPictureSeeder::class,
            \Database\Seeders\CommentSeeder::class,
        ]);

        $response = $this->getJson('api/tours/1');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'name',
                    'picture' => [
                        '*' => [
                            'id',
                            'tour_id',
                            'picture',
                            'main'
                        ]
                    ],
                    'description',
                    'itinerary_sugesstion',
                    'amenities_facilities',
                    'maps',
                    'destination_name',
                    'review_rating',
                    'comment' => [
                        '*' => [
                            'id',
                            'author_id',
                            'type',
                            'tour_id',
                            'blog_local_experience_id',
                            'content',
                            'created_date'
                        ]
                    ]
                ],
            ]);
    }

    // 404: Data not found
    public function testGetSingleDataNotFound()
    {
        /**
         * - assertStatus
         * - assertJson : message
         */
        $response = $this->getJson('api/tours/1');

        $response
            ->assertStatus(404)
            ->assertJson([
                'message' => "Data not found"
            ]);
    }
}
