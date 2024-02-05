<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Destination;
use App\Models\Tour;
use App\Models\TourPicture;
use App\Models\ReviewRating;
use App\Models\Comment;

class TourController extends Controller
{
    public function index()
    {
        $destinations = Destination::all();
        $tours = Tour::all();
        $tourPictures = TourPicture::all();
        $reviews = ReviewRating::all();

        if ($tours->isEmpty())
            return response()->json(['message' => "Data not found"], 400);

        $result = collect($tours)->map(function ($tour) use ($destinations, $reviews, $tourPictures) {
            $destination = collect($destinations)->where('id', $tour['destination_id'])->first();

            $tourPicture = collect($tourPictures)->where('tour_id', $tour['id']);
            $tourPicture2 = collect($tourPicture)->where('main', true)->first();

            $tourReviews = collect($reviews)->where('tour_id', $tour['id']);
            $averageRating = $tourReviews->avg('rating');


            return [
                'id' => $tour['id'],
                'picture' =>  $tourPicture2 !== null ? $tourPicture2['picture'] : null,
                'destination_name' => $destination['name'],
                'name' => $tour['name'],
                'review_rating' => $averageRating,
            ];
        });

        // Urutkan koleksi berdasarkan tourCount secara descending
        $sortedDestinations = $result->sortByDesc('review_rating');

        // Ambil 3 destinasi pertama
        $topDestinations = $sortedDestinations->take(3);


        return response()->json([
            "message" => "Get 3 top success",
            "data" => $topDestinations
        ], 200);
    }

    public function show($id)
    {


        // get data by id
        $tour = Tour::where('id', $id)->first();

        if (!$tour)
            return response()->json(['message' => "Data not found"], 400);


        $destinations = Destination::all();
        $tourPictures = TourPicture::all();
        $reviews = ReviewRating::all();
        $comments = Comment::all();

        //  if($tours->isEmpty()) 
        //      return response()->json(['message' => "Data not found"], 400);

        $destination = collect($destinations)->where('id', $tour['destination_id'])->first();

        $tourPicturess = collect($tourPictures)->where('tour_id', $tour['id']);

        $tourReviews = collect($reviews)->where('tour_id', $tour['id']);
        $averageRating = $tourReviews->avg('rating');

        $commentss = collect($comments)->where('tour_id', $tour['id']);



        $result = [
            'id' => $tour['id'],
            'name' => $tour['name'],
            'picture' =>  $tourPicturess,
            'description' => $tour['description'],
            'itinerary_sugesstion' => $tour['itinerary_sugesstion'],
            'amenities_facilities' => $tour['amenities_facilities'],
            'maps' => $tour['maps'],
            'destination_name' => $destination['name'],
            'review_rating' => $averageRating,
            'comment' => $commentss
        ];

        // response json
        return response()->json([
            "message" => "Get single success",
            "data" => $result
        ], 200);
    }
}
