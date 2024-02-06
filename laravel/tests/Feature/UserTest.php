<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Helpers\JWTAuth;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    use RefreshDatabase; // This trait resets the database after each test

    /**
     * register
     */

    // 200: Success
    public function testRegisterSuccess()
    {
        /**
         * - assertStatus : code http
         * - assertJson : message
         * - assertDatabaseHas : stored data
         * - assertNotNull : token 
         * - assertTrue : password hash
         */
        $userData = [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $response = $this->postJson('api/user/register', $userData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Success',
            ]);

        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
        ]);

        $user = User::where('email', $userData['email'])->firstOrFail();

        $this->assertNotNull($response['token']);
        $this->assertTrue(Hash::check($userData['password'], $user->password));
    }

    // 422: Validation Error
    public function testRegisterValidationError()
    {
        /**
         * - assertStatus : code http
         */
        $userData = [
            'name' => '',
            'email' => '',
            'password' => '',
        ];

        $response = $this->postJson('api/user/register', $userData);

        $response->assertStatus(422);
    }

    // 400: Email already registered
    public function testRegisterEmailAlreadyRegistered()
    {
        /**
         * - assertStatus : code http first user
         * - assertStatus : code http second user
         * - assertJson : message
         */
        $firstUserData = [
            'name' => 'First User',
            'email' => 'duplicate@example.com',
            'password' => 'password',
        ];

        $this->postJson('api/user/register', $firstUserData)->assertStatus(200);

        $secondUserData = [
            'name' => 'Second User',
            'email' => 'duplicate@example.com',
            'password' => 'password',
        ];

        $response = $this->postJson('api/user/register', $secondUserData);

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

    // 200: Success
    public function testLoginSuccess()
    {
        /**
         * - assertStatus : code http register user
         * - assertStatus : code http login user
         * - assertJson : message
         * - assertDatabaseHas : stored data
         * - assertNotNull : token 
         */
        $userData = [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $this->postJson('api/user/register', $userData)->assertStatus(200);

        $response = $this->postJson('api/user/login', [
            'email' => $userData['email'],
            'password' => $userData['password']
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Success',
            ]);

        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
        ]);

        $this->assertNotNull($response['token']);
    }

    // 422: Validation error
    public function testLoginvValidationError()
    {
        /**
         * - assertStatus : code http register user
         * - assertStatus : code http login user
         */
        $userData = [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $this->postJson('api/user/register', $userData)->assertStatus(200);

        $response = $this->postJson('api/user/login', [
            'email' => '',
            'password' => ''
        ]);

        $response->assertStatus(422);
    }

    // 400: Incorrect email or password
    public function testLoginIncorrectEmailOrPassword()
    {
        /**
         * - assertStatus : code http register user
         * - assertStatus : code http login user
         * - assertJson : message
         */
        $userData = [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $this->postJson('api/user/register', $userData)->assertStatus(200);

        $response = $this->postJson('api/user/login', [
            'email' => 'wrong email',
            'password' => 'wrong password'
        ]);

        $response
            ->assertStatus(400)
            ->assertJson([
                "message" => "Email or password wrong"
            ]);
    }

    /**
     * get single
     */

    // 200: Success
    public function testGetSingleSuccess()
    {
        /**
         * - assertStatus : code http
         * - assertJson : message & data user
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
        ])->getJson('api/user');

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Success',
                'data' => [
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                ]
            ]);
    }

    // 401: Invalid token & user not found
    public function testGetSingleInvalidToken()
    {
        /**
         * - assertStatus : code http
         * - assertJson : error
         */
        $response = $this->getJson('api/user');

        $response
            ->assertStatus(401)
            ->assertJson([
                'error' => 'Unauthorized',
            ]);
    }


    /**
     * update single
     */

    // 200: Success
    public function testUpdateSingleSuccess()
    {
        /**
         * - assertStatus : code http
         * - assertJson : message
         * - assertTrue : password different
         * - assertTrue : picture exists
         */
        $oldUser = [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $newUser = [
            'name' => 'Update Test',
            'email' => 'updatetest@example.com',
            'password' => 'updatepassword',
        ];

        $user = new User($oldUser);
        $user->password = Hash::make($oldUser['password']);
        $user->save();

        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->putJson('api/user', [
            'name' => $newUser['name'],
            'email' => $newUser['email'],
            'password' => $newUser['password'],
            'picture' => UploadedFile::fake()->create('image.png', 1000),
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                "message" => "Success",
                "data" => [
                    'name' => $newUser['name'],
                    'email' => $newUser['email'],
                ]
            ]);

        $user = User::where('email', $newUser['email'])->first();

        $this->assertTrue(!Hash::check($oldUser['password'], $user->password));

        $imageName = $user->picture;
        $filePath = public_path('images/user/' . $imageName);

        $this->assertTrue(file_exists($filePath));
    }

    // 422: Validation error
    public function testUpdateSingleValidationError()
    {
        /**
         * - assertStatus : code http
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
        ])->putJson('api/user', [
            'name' => '',
            'email' => '',
            'password' => ''
        ]);

        $response
            ->assertStatus(422);
    }

    // 200: Success (no picture update)
    public function testUpdateSingleSuccessNoPicture()
    {
        /**
         * - assertStatus : code http
         * - assertJson : message
         * - assertTrue : password different
         */
        $oldUser = [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $newUser = [
            'name' => 'Update Test',
            'email' => 'updatetest@example.com',
            'password' => 'updatepassword',
        ];

        $user = new User($oldUser);
        $user->password = Hash::make($oldUser['password']);
        $user->save();

        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->putJson('api/user', [
            'name' => $newUser['name'],
            'email' => $newUser['email'],
            'password' => $newUser['password'],
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                "message" => "Success (no picture update)",
                "data" => [
                    'name' => $newUser['name'],
                    'email' => $newUser['email'],
                ]
            ]);

        $user = User::where('email', $newUser['email'])->first();

        $this->assertTrue(!Hash::check($oldUser['password'], $user->password));
    }

    // 400: Data must be different
    public function testUpdateSingleDataMustBeDifferent()
    {
        /**
         * - assertStatus : code http
         * - assertJson : message
         */
        $dataUser = [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $user = new User($dataUser);
        $user->password = Hash::make($dataUser['password']);
        $user->save();

        $token = JWTAuth::createTokenJwt($user);

        $response = $this->withHeaders([
            'Authorization' =>  $token,
        ])->putJson('api/user', [
            'name' => $dataUser['name'],
            'email' => $dataUser['email'],
            'password' => $dataUser['password'],
        ]);

        $response
            ->assertStatus(400)
            ->assertJson([
                "message" => "Data must be different"
            ]);
    }
}
