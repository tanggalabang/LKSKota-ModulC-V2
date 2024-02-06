<?php

namespace App\Http\Controllers;

use App\Models\Checkout;
use App\Models\ReviewRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ReviewRatingController extends Controller
{
    protected $reviewRating;

    public function __construct(ReviewRating $reviewRating)
    {
        $this->reviewRating = $reviewRating;
    }

    // public function index()
    // {
    //     $reviews = ReviewRating::all();
    //     $tours = Tour::all();
    //     $users = User::all();

    //     if ($reviews->isEmpty())
    //         return response()->json(['message' => "Data not found"], 400);


    //     // Membuat koleksi berdasarkan 'tour_id' dan mengambil review dengan rating tertinggi dari setiap grup
    //     $topReviews = collect($reviews)->groupBy('tour_id')->map(function ($groupedReviews, $tourId) use ($users, $tours) {
    //         // Mengurutkan ulasan berdasarkan rating secara descending
    //         $sortedReviews = $groupedReviews->sortByDesc('rating');

    //         // Mengambil ulasan dengan rating tertinggi
    //         $topReview = $sortedReviews->first();

    //         $author = collect($users)->where('id', $topReview['author_id'])->first();
    //         $tour = collect($tours)->where('id', $topReview['tour_id'])->first();

    //         return [
    //             'id' => $topReview['id'],
    //             'tour_name' => $tour['name'],
    //             'author_name' => $author['name'],
    //             'author_picture' => $author['picture'],
    //             'content' => $topReview['content'],
    //             'rating' => $topReview['rating'],
    //             'created_date' => $topReview['created_date'],
    //         ];
    //     });

    //     // Mengambil 4 review pertama
    //     $topReviews = $topReviews->take(4);

    //     return response()->json([
    //         "message" => "Get 4 top success",
    //         "data" => $topReviews
    //     ], 200);
    // }

    /**
     * get all (top 3)
     * 
     * 200: Success
     * 404: Data not found
     */

    public function index()
    {
        // Gunakan eager loading untuk memuat data terkait dan query builder untuk efisiensi
        $topReviews = ReviewRating::with(['tour', 'author'])
            ->get()
            ->groupBy('tour_id')
            ->map(function ($reviews) {
                // Mengambil ulasan dengan rating tertinggi dari setiap grup
                return $reviews->sortByDesc('rating')->first();
            })
            ->sortByDesc('rating')
            ->take(4)
            ->map(function ($review) {
                // Membentuk ulang data untuk respons
                return [
                    'id' => $review->id,
                    'tour_name' => $review->tour->name, // Memanfaatkan eager loading
                    'author_name' => $review->author->name, // Memanfaatkan eager loading
                    'author_picture' => $review->author->picture, // Memanfaatkan eager loading
                    'content' => $review->content,
                    'rating' => $review->rating,
                    'created_date' => now(), // Format tanggal sesuai kebutuhan
                ];
            })->values();

        if ($topReviews->isEmpty())
            return response()->json(['message' => "Data not found"], 404);

        // dd(response()->json($topReviews));
        return response()->json([
            "message" => "Get 4 top success",
            "data" => $topReviews
        ], 200);
    }


    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'tour_id' => 'required',
    //         'rating' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()->toArray()], 422);
    //     }

    //     $checkouts = Checkout::where('tour_id', $request->tour_id)->where('user_id', $request->user->id)->count();

    //     if ($checkouts === 0) return response()->json(['message' => 'You not have booking in this tour'], 400);



    //     // Get credentials
    //     $credentials = collect($request->only($this->reviewRating->getFillable()))
    //         ->put('author_id', $request->user->id)
    //         ->put('created_date', now())
    //         ->toArray();

    //     $review = $this->reviewRating->create($credentials);

    //     return response()->json([
    //         'message' => 'Success'
    //     ], 200);
    // }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tour_id' => 'required',
            'rating' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $hasBooking = Checkout::where('tour_id', $request->tour_id)
            ->where('user_id', $request->user->id)
            ->first();

        if (!$hasBooking) {
            return response()->json(['message' => 'You have not booked this tour'], 400);
        }

        if ($hasBooking->status !== 'done') {
            return response()->json(['message' => 'Status must be done'], 400);
        }

        $credentials = collect($request->only($this->reviewRating->getFillable()))
            ->put('author_id', $request->user->id)
            ->put('created_date', now())
            ->toArray();

        $this->reviewRating->create($credentials);

        return response()->json(['message' => 'Success'], 201);
    }

    public function show(Request $request, $id)
    {

        $review = ReviewRating::find($id);
        // dd($review);

        if (!$review) {
            return response()->json(['message' => "Data not found"], 404);
        }

        if ($review->author_id !== $request->user->id) {
            return response()->json(['message' => "User not created the review"], 400);
        }


        $hasBooking = Checkout::where('tour_id', $review->tour_id)
            ->where('user_id', $request->user->id)
            ->first();

        if (!$hasBooking) {
            return response()->json(['message' => 'You have not booked this tour'], 400);
        }

        if ($hasBooking->status !== 'done') {
            return response()->json(['message' => 'Status must be done'], 400);
        }

        return response()->json([
            'message' => "Success",
            'data' => $review
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $review = ReviewRating::find($id);

        if (!$review) {
            return response()->json(['message' => "Data not found"], 404);
        }

        if ($review->author_id !== $request->user->id) {
            return response()->json(['message' => "User not created the review"], 400);
        }

        $hasBooking = Checkout::where('tour_id', $review->id)
            ->where('user_id', $request->user->id)
            ->first();

        if (!$hasBooking) {
            return response()->json(['message' => 'You have not booked this tour'], 400);
        }

        if ($hasBooking->status !== 'done') {
            return response()->json(['message' => 'Status must be done'], 400);
        }

        $credentials = collect($request->only($this->reviewRating->getFillable()))
            ->toArray();

        // dd($credentials);

        if (
            $request->rating === $credentials['rating'] &&
            $request->content === $credentials['content']
        ) {
            return response()->json(["message" => "Data must be different"], 400);
        }


        $review->update($credentials);

        $updatedReview = ReviewRating::find($id);

        // dd($updatedReview);

        return response()->json(['message' => 'Success', 'data' => $updatedReview], 200);
    }

    public function destroy(Request $request, $id)
    {
        $review = ReviewRating::find($id);

        if (!$review) {
            return response()->json(['message' => "Data not found"], 404);
        }

        if ($review->author_id !== $request->user->id) {
            return response()->json(['message' => "User not created the review"], 400);
        }

        $hasBooking = Checkout::where('tour_id', $review->id)
            ->where('user_id', $request->user->id)
            ->first();

        if (!$hasBooking) {
            return response()->json(['message' => 'You have not booked this tour'], 400);
        }

        if ($hasBooking->status !== 'done') {
            return response()->json(['message' => 'Status must be done'], 400);
        }

        $review->delete();

        return response()->json([
            'message' => "Success",
        ], 200);
    }
}
