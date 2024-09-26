<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\CourseController;


//courses
Route::get('/courses/recommendation', [CourseController::class, 'getRecommendation']); // GET COURSES WITHIN TOP 4 RATINGS

//rating
Route::get('/courses/ratings/top-reviews', [RatingController::class, 'getTopReview']); // GET TOP 3 REVIEWS