<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\CategoryController;

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
Route::post('/register/{role}', [AuthController::class, 'register']); // REGISTER USER & INSTRUCTOR
Route::post('/login', [AuthController::class, 'login']); // LOGIN USER
Route::get('/{user_id}/profile', [AuthController::class, 'getProfile']); // GET USER DATA
Route::put('/{user_id}/profile', [AuthController::class, 'updateProfile']); // UPDATE USER DATA

//Threads
Route::get('/threads', [ThreadController::class, 'getThreads']); // GET ALL THREADS
Route::post('/threads', [ThreadController::class, 'createThread']); // CREATE NEW THREAD
Route::get('/threads/{thread_id}', [ThreadController::class, 'getThreadDetail']); // GET THREAD DETAIL
Route::post('/threads/{thread_id}/comment', [ThreadController::class, 'createComment']); // CREATE COMMENT ON THREAD

//Categories
Route::get('/categories', [CategoryController::class, 'getCategories']); // GET ALL CATEGORIES