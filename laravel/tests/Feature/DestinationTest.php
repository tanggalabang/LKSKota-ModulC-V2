<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestinationTest extends TestCase
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
            \Database\Seeders\DestinationSeeder::class,
            \Database\Seeders\TourSeeder::class,
        ]);

        $response = $this->getJson('api/destinations');

        $response
            ->assertStatus(200)
            // ->assertJson([
            //     'message' => 'Get 3 top success',
            //     "data" => [
            //         [
            //             'id' => 1,
            //             'name' => 'Indonesia',
            //             'picture' => 'test.jpg',
            //             'tours' => 3
            //         ],
            //         [
            //             'id' => 2,
            //             'name' => 'Amerika',
            //             'picture' => 'test.jpg',
            //             'tours' => 2
            //         ],
            //         [
            //             'id' => 3,
            //             'name' => 'Jepang',
            //             'picture' => 'test.jpg',
            //             'tours' => 1,
            //         ]
            //     ]
            // ]);
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['id', 'name', 'picture', 'tours'],
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
        $response = $this->getJson('api/destinations');

        $response
            ->assertStatus(404)
            ->assertJson([
                'message' => "Data not found"
            ]);
    }
}
