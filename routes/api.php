<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\CourseController;

Route::get('/test', function(){
    return response()->json([
        'message' => 'Hello World!'
    ]);}
); //to test connectivity





//courses
Route::get('/courses', [CourseController::class, 'getCourse']);
Route::get('/courses/recommendation', [CourseController::class, 'getRecommendation']); // GET COURSES WITHIN TOP 4 RATINGS


//rating
Route::get('/courses/ratings/top-reviews', [RatingController::class, 'getTopReviews']); // GET TOP 3 REVIEWS

