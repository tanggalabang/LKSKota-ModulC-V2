<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JWTAuth;
use App\Models\User;
use Hash;
use Validator;

class UserController extends Controller
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function register(Request $request)
    {
        // validation
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);

        // if email already registered
        if (User::where('email', $data['email'])->count() == 1) {
            return response()->json([
                "errors" => [
                    "email" => [
                        "username already registered"
                    ]
                ]
            ], 400);
        }

        // get data from request
        $user = new User($data);
        $user->password = Hash::make($request->password);

        // create data form request
        $user->save();

        // create token
        $token = JWTAuth::createTokenJwt($user);

        // response json
        return response()->json([
            'message' => 'Success',
            'token' => $token
        ], 200);
    }

    public function login(Request $request)
    {
        // validation
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        // get user form email request
        $user = User::where('email', $request->email)->first();

        // if usre not found
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email or password wrong'], 400);
        }

        // create token
        $token = JWTAuth::createTokenJwt($user);

        // response json 
        return response()->json([
            'message' => 'Success',
            'token' => $token
        ], 200);
    }

    public function show(Request $request)
    {
        return response()->json([
            "message" => "Success",
            "data" => $request->user
        ], 200);
    }

    public function update(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'picture' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // If validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->toArray()], 422);
        }

        // define $image 
        $oldImage = $request->user->picture;
        $newImage = null;

        $responseMessage = 'Success (no picture update)';

        // if has image 
        if ($request->hasFile('picture') && $oldImage) {

            // naming and placing image
            $imageName = time() . '.' . $request->picture->extension();
            $request->picture->move(public_path('images/user'), $imageName);
            $newImage = $imageName;

            // delete old image
            if (file_exists(public_path('images/user') . '/' . $oldImage)) {
                unlink(public_path('images/user') . '/' . $oldImage);
            }

            $responseMessage = 'Success';
        } else if ($request->hasFile('picture')) {
            $imageName = time() . '.' . $request->picture->extension();
            $request->picture->move(public_path('images/user'), $imageName);
            $newImage = $imageName;

            $responseMessage = 'Success';
        }

        // Get credentials
        $credentials = collect($request->only($this->user->getFillable()))
            ->put('password', Hash::make($request->password))
            ->put('picture', $newImage)
            ->toArray();

        // Get user by id
        $user = User::find($request->user->id);

        // Check if the data is the same
        if (
            $user->name === $credentials['name'] &&
            Hash::check($request->password, $user->password) &&
            $user->email === $credentials['email'] &&
            !$request->picture
        ) {
            return response()->json(["message" => "Data must be different"], 400);
        }

        
        // Update user
        $updated = $user->update($credentials);
        
        // dd($updated);
        // Response json
        return response()->json([
            "message" => $responseMessage,
            "data" => $updated
        ], 200);
    }
}
