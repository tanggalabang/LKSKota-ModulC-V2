<?php

namespace App\Http\Controllers;

use App\Models\Tour;

class TourController extends Controller
{

    // public function index()
    // {
    //     $destinations = Destination::all();
    //     $tours = Tour::all();
    //     $tourPictures = TourPicture::all();
    //     $reviews = ReviewRating::all();

    //     if ($tours->isEmpty())
    //         return response()->json(['message' => "Data not found"], 400);

    //     $result = collect($tours)->map(function ($tour) use ($destinations, $reviews, $tourPictures) {
    //         $destination = collect($destinations)->where('id', $tour['destination_id'])->first();

    //         $tourPicture = collect($tourPictures)->where('tour_id', $tour['id']);
    //         $tourPicture2 = collect($tourPicture)->where('main', true)->first();

    //         $tourReviews = collect($reviews)->where('tour_id', $tour['id']);
    //         $averageRating = $tourReviews->avg('rating');


    //         return [
    //             'id' => $tour['id'],
    //             'picture' =>  $tourPicture2 !== null ? $tourPicture2['picture'] : null,
    //             'destination_name' => $destination['name'],
    //             'name' => $tour['name'],
    //             'review_rating' => $averageRating,
    //         ];
    //     });

    //     // Urutkan koleksi berdasarkan tourCount secara descending
    //     $sortedDestinations = $result->sortByDesc('review_rating');

    //     // Ambil 3 destinasi pertama
    //     $topDestinations = $sortedDestinations->take(3);


    //     return response()->json([
    //         "message" => "Get 3 top success",
    //         "data" => $topDestinations
    //     ], 200);
    // }

    public function index()
    {
        // Gunakan eager loading untuk mengambil tours dengan destination, main picture, dan average rating
        $tours = Tour::with(['destination', 'tourPictures' => function ($query) {
            $query->where('main', true); // Ambil hanya gambar utama
        }])
            ->withAvg('reviewRatings', 'rating') // Hitung average rating
            ->get();

        if ($tours->isEmpty())
            return response()->json(['message' => "Data not found"], 404);

        $result = $tours->map(function ($tour) {
            // Ambil gambar utama dari relationship
            $tourPicture = $tour->tourPictures->first();

            return [
                'id' => $tour->id,
                'picture' => $tourPicture ? $tourPicture->picture : null,
                'destination_name' => $tour->destination->name,
                'name' => $tour->name,
                'review_rating' => $tour->review_ratings_avg_rating, // Akses average rating
            ];
        });

        // Urutkan berdasarkan review rating secara descending dan ambil 3 teratas
        $topTours = $result->sortByDesc('review_rating')->take(3);

        return response()->json([
            "message" => "Get 3 top success",
            "data" => $topTours
        ], 200);
    }


    // public function show($id)
    // {


    //     // get data by id
    //     $tour = Tour::where('id', $id)->first();

    //     if (!$tour)
    //         return response()->json(['message' => "Data not found"], 400);


    //     $destinations = Destination::all();
    //     $tourPictures = TourPicture::all();
    //     $reviews = ReviewRating::all();
    //     $comments = Comment::all();

    //     //  if($tours->isEmpty()) 
    //     //      return response()->json(['message' => "Data not found"], 400);

    //     $destination = collect($destinations)->where('id', $tour['destination_id'])->first();

    //     $tourPicturess = collect($tourPictures)->where('tour_id', $tour['id']);

    //     $tourReviews = collect($reviews)->where('tour_id', $tour['id']);
    //     $averageRating = $tourReviews->avg('rating');

    //     $commentss = collect($comments)->where('tour_id', $tour['id']);



    //     $result = [
    //         'id' => $tour['id'],
    //         'name' => $tour['name'],
    //         'picture' =>  $tourPicturess,
    //         'description' => $tour['description'],
    //         'itinerary_sugesstion' => $tour['itinerary_sugesstion'],
    //         'amenities_facilities' => $tour['amenities_facilities'],
    //         'maps' => $tour['maps'],
    //         'destination_name' => $destination['name'],
    //         'review_rating' => $averageRating,
    //         'comment' => $commentss
    //     ];

    //     // response json
    //     return response()->json([
    //         "message" => "Get single success",
    //         "data" => $result
    //     ], 200);
    // }

    public function show($id)
    {
        // Gunakan eager loading untuk memuat data terkait secara efisien
        $tour = Tour::with(['destination', 'tourPictures', 'reviewRatings', 'comments'])
            ->withAvg('reviewRatings as review_rating', 'rating') // Menghitung rata-rata rating
            ->find($id); // Menggantikan `where('id', $id)->first()` dengan `find($id)`

        if (!$tour) {
            return response()->json(['message' => "Data not found"], 404);
        }

        // Mengkonstruksi respons dengan data yang sudah dimuat
        $result = [
            'id' => $tour->id,
            'name' => $tour->name,
            'picture' =>  $tour->tourPictures->map(function ($picture) {
                return [
                    'id' => $picture->id,
                    'tour_id' => $picture->tour_id,
                    'picture' => $picture->picture,
                    'main' => $picture->main,
                ];
            }),
            'description' => $tour->description,
            'itinerary_sugesstion' => $tour->itinerary_sugesstion,
            'amenities_facilities' => $tour->amenities_facilities,
            'maps' => $tour->maps,
            'destination_name' => $tour->destination ? $tour->destination->name : null,
            'review_rating' => $tour->review_rating, // Memanfaatkan rata-rata rating yang sudah dihitung
            'comment' => $tour->comments->map(function ($comment) {
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
            "message" => "Get single success",
            "data" => $result
        ], 200);
    }
}
