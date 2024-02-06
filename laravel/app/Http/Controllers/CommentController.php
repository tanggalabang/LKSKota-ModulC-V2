<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    protected $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $credentials = collect($request->only($this->comment->getFillable()))
            ->put('author_id', $request->user->id)
            ->put('created_date', now())
            ->toArray();

        $this->comment->create($credentials);

        return response()->json(['message' => 'Success'], 201);
    }

    public function show(Request $request, $id)
    {
        $data = Comment::find($id);

        if (!$data) {
            return response()->json(['message' => "Data not found"], 404);
        }

        if ($data->author_id !== $request->user->id) {
            return response()->json(['message' => "User not created the comment"], 400);
        }

        return response()->json([
            'message' => "Success",
            'data' => $data
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $data = Comment::find($id);

        if (!$data) {
            return response()->json(['message' => "Data not found"], 404);
        }
        if ($data->author_id !== $request->user->id) {
            return response()->json(['message' => "User not created the comment"], 400);
        }

        $credentials = collect($request->only($this->comment->getFillable()))
            ->toArray();

        if (
            $request->type === $credentials['type'] &&
            $request->tour_id === $credentials['tour_id'] &&
            $request->blog_local_experience_id === $credentials['blog_local_experience_id'] &&
            $request->content === $credentials['content']
        ) {
            return response()->json(["message" => "Data must be different"], 400);
        }


        $data->update($credentials);

        $updatedData = Comment::find($id);


        return response()->json(['message' => 'Success', 'data' => $updatedData], 200);
    }

    public function destroy(Request $request, $id)
    {
        $data = Comment::find($id);

        if (!$data) {
            return response()->json(['message' => "Data not found"], 404);
        }

        if ($data->author_id !== $request->user->id) {
            return response()->json(['message' => "User not created the comment"], 400);
        }

        $data->delete();

        return response()->json([
            'message' => "Success",
        ], 200);
    }
}
