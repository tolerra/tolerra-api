<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\AdminController;


Route::get('/test', function(){
    return response()->json([
        'message' => 'Hello World!'
    ]);
}); //to test connectivity

//courses
Route::get('/courses', [CourseController::class, 'getCourse']);
Route::get('/courses/recommendation', [CourseController::class, 'getRecommendation']); // GET COURSES WITHIN TOP 4 RATINGS
Route::get('/courses/{course_id}', [CourseController::class, 'getCourseDetail']); // GET COURSE DETAIL

//categories
Route::get('/categories', [CategoryController::class, 'getCategories']); // GET ALL CATEGORIES

//rating
Route::get('/courses/ratings/top-reviews', [RatingController::class, 'getTopReviews']); // GET TOP 3 REVIEWS
Route::get('/courses/{course_id}/ratings', [RatingController::class, 'getCourseRatings']); // GET COURSE RATINGS


Route::post('/register/{role}', [AuthController::class, 'register']); // REGISTER USER & INSTRUCTOR
Route::post('/login', [AuthController::class, 'login']); // LOGIN USER

Route::get('/threads', [ThreadController::class, 'getThreads']); // GET ALL THREADS
Route::get('/threads/{thread_id}', [ThreadController::class, 'getThreadDetail']); // GET THREAD DETAIL

//Need Token
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/{user_id}/profile', [UserController::class, 'getProfile']); // GET USER DATA
    Route::put('/user/{user_id}/profile', [UserController::class, 'updateProfile']); // UPDATE USER DATA

    //Courses
    Route::post('/instructor/courses', [CourseController::class, 'addCourse']);
    Route::get('/courses/enrolled/{course_id}', [CourseController::class, 'getEnrolledDetailCourse']);
    Route::get('/courses/instructor/{instructor_id}', [CourseController::class, 'getInstructorCourses']); // GET INSTRUCTOR COURSES
    Route::put('/courses/{course_id}', [CourseController::class, 'updateCourse']); // UPDATE COURSE
    Route::delete('/courses/{course_id}', [CourseController::class, 'deleteCourse']); // DELETE COURSE
    

    //Chapter
    Route::post('/instructor/courses/{course_id}/chapter', [ChapterController::class, 'addChapter']); // ADD CHAPTER TO COURSE
    Route::get('/courses/{course_id}/chapter/{chapter_id}', [ChapterController::class, 'getChapterDetail']); // GET CHAPTER DETAIL
    Route::put('/instructor/courses/{course_id}/chapter/{chapter_id}', [ChapterController::class, 'updateChapter']); // UPDATE CHAPTER
    Route::delete('/instructor/courses/{course_id}/chapter/{chapter_id}', [ChapterController::class, 'deleteChapter']); // DELETE CHAPTER
    Route::get('/courses/{course_id}/chapters', [ChapterController::class, 'getChapters']); // GET ALL CHAPTERS

    //Threads
    Route::post('/threads', [ThreadController::class, 'createThread']); // CREATE NEW THREAD
    Route::post('/threads/{thread_id}/comment', [ThreadController::class, 'createComment']); // CREATE COMMENT ON THREAD

    //Enrollments
    Route::post('/student/courses/{course_id}/enroll', [EnrollmentController::class, 'enroll']);
    Route::get('/student/enrolled-course', [EnrollmentController::class, 'getEnrolledCourses']);

    // Notifications
    Route::get('/notifications/{user_id}', [NotificationController::class, 'getNotifications']);
    Route::put('/notifications/{user_id}/{notification_id}', [NotificationController::class, 'updateNotification']);
});


    //Rating
    Route::post('/courses/{course_id}/rate', [RatingController::class, 'addRating']);

//For Admin
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/admin/disability-verifications', [AdminController::class, 'viewDisabilityVerifications']);
    Route::get('/admin/disability-verifications/{id}', [AdminController::class, 'viewDisabilityVerification']);
    Route::put('/admin/disability-verifications/{id}', [AdminController::class, 'updateDisabilityVerification']);
    Route::get('/admin/courses', [CourseController::class, 'getCourse']);
    Route::get('/admin/courses/{course_id', [CourseController::class, 'getCourseDetail']);
    Route::get('/admin/courses/{course_id}/chapters', [ChapterController::class, 'getChapterDetail']);
});

