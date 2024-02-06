<?php

namespace App\Http\Controllers;

use App\Models\BlogLocalExperience;
use App\Models\Checkout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlogLocalExperienceController extends Controller
{
    protected $blogModel;

    public function __construct(BlogLocalExperience $blogModel)
    {
        $this->blogModel = $blogModel;
    }

    public function index()
    {
        $blogs = BlogLocalExperience::with(['author', 'tour'])
            ->where('tour_id', null)
            ->get();

        if ($blogs->isEmpty())
            return response()->json(['message' => "Data not found"], 404);

        $result = $blogs->map(function ($blog) {
            return [
                'id' => $blog->id,
                'title' => $blog->title,
                'picture' => $blog->picture,
                'autor_name' => $blog->author->name,
                'author_picture' => $blog->author->picture,
                'content' => $blog->content,
                'tour_id' => $blog->tour_id,
                'create_date' => now(),
            ];
        });

        if ($result->isEmpty())
            return response()->json(['message' => "Data not found"], 404);


        // dd($result);

        return response()->json([
            "message" => "Success",
            "data" => $result
        ], 200);
    }
    public function index2()
    {
        $blogs = BlogLocalExperience::with(['author', 'tour'])
            ->where('tour_id', true)
            ->get();

        if ($blogs->isEmpty())
            return response()->json(['message' => "Data not found"], 404);

        $result = $blogs->map(function ($blog) {
            return [
                'id' => $blog->id,
                'title' => $blog->title,
                'picture' => $blog->picture,
                'autor_name' => $blog->author->name,
                'author_picture' => $blog->author->picture,
                'content' => $blog->content,
                'tour_id' => $blog->tour_id,
                'create_date' => now(),
            ];
        });

        if ($result->isEmpty())
            return response()->json(['message' => "Data not found"], 404);


        // dd($result);

        return response()->json([
            "message" => "Success",
            "data" => $result
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            "picture" => "required",
            "content" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }



        $responseMessage = 'Success blog';

        if ($request->tour_id) {
            $responseMessage = 'Success local experience';

            $hasBooking = Checkout::where('user_id', $request->user->id)
                ->first();

            if (!$hasBooking) {
                return response()->json(['message' => 'User has not checked out (local experience)'], 400);
            }
        }

        BlogLocalExperience::create(
            $validator->validated()
                + ['user_id' => $request->user->id]
                + ['created_date' => now()]
        );

        return response()->json([
            'message' => $responseMessage
        ], 201);
    }

    public function show($id)
    {
        $blog = BlogLocalExperience::with(['author', 'tour', 'comments'])
            ->find($id);

        if (!$blog) {
            return response()->json(['message' => "Data not found"], 404);
        }

        $result = [
            'id' => $blog->id,
            'title' => $blog->title,
            'picture' => $blog->picture,
            'author_name' => $blog->author->name,
            'author_picture' => $blog->author->picture,
            'created_date' => $blog->created_date,
            'content' => $blog->content,
            'comments' => $blog->comments->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'author_id' => $comment->author_id,
                    'type' => $comment->type,
                    'tour_id' => $comment->tour_id,
                    'blog_local_experience_id' => $comment->blog_local_experience_id,
                    'content' => $comment->content,
                    'created_date' => $comment->created_at, // Asumsi 'created_date' adalah 'created_at'
                ];
            })
        ];

        return response()->json([
            "message" => "Success",
            "data" => $result
        ], 200);
    }

    public function show2(Request $request, $id)
    {
        $blog = BlogLocalExperience::find($id);

        if (!$blog) {
            return response()->json(['message' => "Data not found"], 404);
        }

        if ($blog->author_id !== $request->user->id) {
            return response()->json(['message' => "User not created the blog / local experience"], 400);
        }

        if ($blog->tour_id) {
            $hasBooking = Checkout::where('user_id', $request->user->id)
                ->first();

            if (!$hasBooking) {
                return response()->json(['message' => 'User has not checked out (local experience)'], 400);
            }
        }

        $result = [
            'id' => $blog->id,
            'title' => $blog->title,
            'picture' => $blog->picture,
            'created_date' => $blog->created_date,
            'content' => $blog->content,
            'tour_id' => $blog->tour_id,
        ];

        return response()->json([
            "message" => "Success",
            "data" => $result
        ], 200);
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $blog = BlogLocalExperience::find($id);

        if (!$blog) {
            return response()->json(['message' => "Data not found"], 404);
        }

        if ($blog->author_id !== $request->user->id) {
            return response()->json(['message' => "User not created the blog / local experience"], 400);
        }

        if ($request->tour_id) {
            $hasBooking = Checkout::where('user_id', $request->user->id)
                ->first();

            if (!$hasBooking) {
                return response()->json(['message' => 'User has not checked out (local experience)'], 400);
            }
        }

        $credentials = collect($request->only($this->blogModel->getFillable()))
            ->toArray();


        if (
            $blog->title === $credentials['title'] &&
            $blog->picture === $credentials['picture'] &&
            $blog->content === $credentials['content'] &&
            !$credentials['tour_id']
        ) {
            return response()->json(["message" => "Data must be different"], 400);
        }

        $blog->update($credentials);

        $updatedData = BlogLocalExperience::find($id);
        return response()->json(['message' => 'Success', 'data' => $updatedData], 200);
    }

    public function destroy(Request $request, $id)
    {
        $blog = BlogLocalExperience::find($id);

        if (!$blog) {
            return response()->json(['message' => "Data not found"], 404);
        }

        if ($blog->author_id !== $request->user->id) {
            return response()->json(['message' => "User not created the blog / local experience"], 400);
        }
        
        if ($blog->tour_id) {
            $hasBooking = Checkout::where('user_id', $request->user->id)
                ->first();

            if (!$hasBooking) {
                return response()->json(['message' => 'User has not checked out (local experience)'], 400);
            }
        }


        $blog->delete();

        return response()->json(['message' => 'Success'], 200);
    }
}
