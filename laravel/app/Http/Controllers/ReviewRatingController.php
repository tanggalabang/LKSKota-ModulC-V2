<?php

namespace App\Http\Controllers;

use App\Models\ReviewRating;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;

class ReviewRatingController extends Controller
{
    protected $reviewRating;

    public function __construct(ReviewRating $reviewRating)
    {
        $this->reviewRating = $reviewRating;
    }

    public function index()
    {
        $reviews = ReviewRating::all();
        $tours = Tour::all();
        $users = User::all();

        if ($reviews->isEmpty())
            return response()->json(['message' => "Data not found"], 400);


        // Membuat koleksi berdasarkan 'tour_id' dan mengambil review dengan rating tertinggi dari setiap grup
        $topReviews = collect($reviews)->groupBy('tour_id')->map(function ($groupedReviews, $tourId) use ($users, $tours) {
            // Mengurutkan ulasan berdasarkan rating secara descending
            $sortedReviews = $groupedReviews->sortByDesc('rating');

            // Mengambil ulasan dengan rating tertinggi
            $topReview = $sortedReviews->first();

            $author = collect($users)->where('id', $topReview['author_id'])->first();
            $tour = collect($tours)->where('id', $topReview['tour_id'])->first();

            return [
                'id' => $topReview['id'],
                'tour_name' => $tour['name'],
                'author_name' => $author['name'],
                'author_picture' => $author['picture'],
                'content' => $topReview['content'],
                'rating' => $topReview['rating'],
                'created_date' => $topReview['created_date'],
            ];
        });

        // Mengambil 4 review pertama
        $topReviews = $topReviews->take(4);

        return response()->json([
            "message" => "Get 4 top success",
            "data" => $topReviews
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tour_id' => 'required',
            'rating' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->toArray()], 422);
        }

        

        // Get credentials
        $credentials = collect($request->only($this->reviewRating->getFillable()))
            ->put('author_id', $request->user->id)
            ->put('created_date', now())
            ->toArray();

        $review = $this->reviewRating->create($credentials);

        return response()->json([
            'message' => 'Create success'
        ], 200);
    }
}
