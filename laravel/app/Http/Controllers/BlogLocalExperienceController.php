<?php

namespace App\Http\Controllers;

use App\Models\BlogLocalExperience;
use App\Models\Checkout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlogLocalExperienceController extends Controller
{
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

            dd($blog);

        if (!$blog) {
            return response()->json(['message' => "Data not found"], 404);
        }

        $result = [
            'id' => $blog->id,
            'title' => $blog->title,
            'picture' => $blog->picture,
            'author_name' => $blog->auhtor->name,
            'author_picture' => $blog->auhtor->picture,
            'created_date' => $blog->created_date,
            'content' => $blog->content,
            'comment' => $blog->comments->map(function ($comment) {
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

        dd($result);

        return response()->json([
            "message" => "Success",
            "data" => $result
        ], 200);
    }
}
