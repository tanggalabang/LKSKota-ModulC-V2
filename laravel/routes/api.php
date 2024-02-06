<?php

use Illuminate\Support\Facades\Route;

Route::post('user/register', [App\Http\Controllers\UserController::class, 'register']);
Route::post('user/login', [App\Http\Controllers\UserController::class, 'login']);

Route::middleware('jwt')->group(function () {
    Route::get('user', [App\Http\Controllers\UserController::class, 'show']);
    Route::put('user', [App\Http\Controllers\UserController::class, 'update']);
    
    Route::post('reviews_testimonials', [App\Http\Controllers\ReviewRatingController::class, 'store']);
    Route::get('reviews_testimonials/{id}', [App\Http\Controllers\ReviewRatingController::class, 'show']);
    Route::put('reviews_testimonials/{id}', [App\Http\Controllers\ReviewRatingController::class, 'update']);
    Route::delete('reviews_testimonials/{id}', [App\Http\Controllers\ReviewRatingController::class, 'destroy']);
     
    Route::post('comment', [App\Http\Controllers\CommentController::class, 'store']);
    Route::get('comment/{id}', [App\Http\Controllers\CommentController::class, 'show']);
    Route::put('comment/{id}', [App\Http\Controllers\CommentController::class, 'update']);
    Route::delete('comment/{id}', [App\Http\Controllers\CommentController::class, 'destroy']);

    Route::post('blog_local_experience', [App\Http\Controllers\BlogLocalExperienceController::class, 'store']);
    Route::put('blog_local_experience/{id}', [App\Http\Controllers\BlogLocalExperienceController::class, 'update']);
    Route::delete('blog_local_experience/{id}', [App\Http\Controllers\BlogLocalExperienceController::class, 'destroy']);
    Route::get('blog_local_experience_2/{id}', [App\Http\Controllers\BlogLocalExperienceController::class, 'show2']);

    Route::post('checkout', [App\Http\Controllers\CheckoutController::class, 'store']);
});

Route::get('destinations', [App\Http\Controllers\DestinationController::class, 'index']);
Route::get('tours', [App\Http\Controllers\TourController::class, 'index']);
Route::get('tours/{id}', [App\Http\Controllers\TourController::class, 'show']);
Route::get('reviews_testimonials', [App\Http\Controllers\ReviewRatingController::class, 'index']);
Route::get('blog_local_experience', [App\Http\Controllers\BlogLocalExperienceController::class, 'index']);
Route::get('blog_local_experience_2', [App\Http\Controllers\BlogLocalExperienceController::class, 'index2']);
Route::get('blog_local_experience/{id}', [App\Http\Controllers\BlogLocalExperienceController::class, 'show']);
