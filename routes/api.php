<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\UserController;

Route::get('/test', function(){
    return response()->json([
        'message' => 'Hello World!'
    ]);}
); //to test connectivity





//courses
Route::get('/courses', [CourseController::class, 'getCourse']);
Route::get('/courses/recommendation', [CourseController::class, 'getRecommendation']); // GET COURSES WITHIN TOP 4 RATINGS
Route::get('/courses/{course_id}', [CourseController::class, 'getCourseDetail']); // GET COURSE DETAIL


//rating
Route::get('/courses/ratings/top-reviews', [RatingController::class, 'getTopReviews']); // GET TOP 3 REVIEWS


//Auth
Route::post('/register/{role}', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/{user_id}/profile', [UserController::class, 'getProfile']);
Route::put('/{user_id}/profile', [UserController::class, 'updateProfile']);
