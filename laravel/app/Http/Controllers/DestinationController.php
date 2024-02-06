<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Destination;
use App\Models\Tour;

class DestinationController extends Controller
{
    // get 3 top destinations
    // public function index(){
    //     $destinations = Destination::all();
    //     $tours = Tour::all();

    //     if($destinations->isEmpty()) 
    //         return response()->json(['message' => "Data not found"], 400);

    //     $destinationsWithTours = collect($destinations)->map(function ($destination) use ($tours) {
    //         $tourCount = collect($tours)->where('destination_id', $destination['id'])->count();
    //         return [
    //             'id' => $destination['id'],
    //             'name' => $destination['name'],
    //             'picture' => $destination['picture'],
    //             'tours' => $tourCount,
    //         ];
    //     });

    //     // Urutkan koleksi berdasarkan tourCount secara descending
    //     $sortedDestinations = $destinationsWithTours->sortByDesc('tours');

    //     // Ambil 3 destinasi pertama
    //     $topDestinations = $sortedDestinations->take(3);
        
    //     return response()->json([
    //         "message" => "Get 3 top success",
    //         "data" => $topDestinations
    //     ], 200);
    // }

    public function index() {
        $destinations = Destination::withCount('tours')
            ->orderByDesc('tours_count')
            ->take(3)
            ->get();
    
        if ($destinations->isEmpty()) {
            return response()->json(['message' => "Data not found"], 404); 
        }
    
        $formattedDestinations = $destinations->map(function ($destination) {
            return [
                'id' => $destination->id,
                'name' => $destination->name,
                'picture' => $destination->picture,
                'tours' => $destination->tours_count, // Use the `tours_count` attribute
            ];
        });

        // for easy test the sort data
        // dd($formattedDestinations);
    
        return response()->json([
            "message" => "Get 3 top success",
            "data" => $formattedDestinations
        ], 200);
    }
    
}
