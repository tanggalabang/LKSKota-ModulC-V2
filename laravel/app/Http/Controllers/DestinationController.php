<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Destination;
use App\Models\Tour;

class DestinationController extends Controller
{
    // get 3 top destinations
    public function index(){
        $destinations = Destination::all();
        $tours = Tour::all();

        if($destinations->isEmpty()) 
            return response()->json(['message' => "Data not found"], 400);

        $destinationsWithTours = collect($destinations)->map(function ($destination) use ($tours) {
            $tourCount = collect($tours)->where('destination_id', $destination['id'])->count();
            return [
                'id' => $destination['id'],
                'name' => $destination['name'],
                'picture' => $destination['picture'],
                'tours' => $tourCount,
            ];
        });

        // Urutkan koleksi berdasarkan tourCount secara descending
        $sortedDestinations = $destinationsWithTours->sortByDesc('tours');

        // Ambil 3 destinasi pertama
        $topDestinations = $sortedDestinations->take(3);
        
        return response()->json([
            "message" => "Get 3 top success",
            "data" => $topDestinations
        ], 200);
    }
}
