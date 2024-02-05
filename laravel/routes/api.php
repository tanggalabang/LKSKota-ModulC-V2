<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('user/register', [App\Http\Controllers\UserController::class, 'register']);
Route::post('user/login', [App\Http\Controllers\UserController::class, 'login']);

Route::middleware('jwt')->group(function () {
    Route::get('user', [App\Http\Controllers\UserController::class, 'show']);
    Route::put('user', [App\Http\Controllers\UserController::class, 'update']);
    
    Route::post('reviews_testimonials', [App\Http\Controllers\ReviewRatingController::class, 'store']);
    Route::put('reviews_testimonials/{id}', [App\Http\Controllers\ReviewRatingController::class, 'update']);
    Route::delete('reviews_testimonials/{id}', [App\Http\Controllers\ReviewRatingController::class, 'destroy']);
});

Route::get('destinations', [App\Http\Controllers\DestinationController::class, 'index']);
Route::get('tours', [App\Http\Controllers\TourController::class, 'index']);
Route::get('tours/{id}', [App\Http\Controllers\TourController::class, 'show']);
Route::get('reviews_testimonials', [App\Http\Controllers\ReviewRatingController::class, 'index']);
