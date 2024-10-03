<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Enrollment;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    public function enroll($course_id)
    {
        $user = Auth::user();
        $course = Course::findOrFail($course_id);

        $activeEnrollments = Enrollment::where('student_id', $user->id)
            ->where('isCompleted', false)
            ->count();

        if ($activeEnrollments >= 2) {
            return response()->json(['message' => 'Cannot enroll in more than 2 active courses'], 400);
        }

        if ($course->isCompleted) {
            return response()->json(['message' => 'Cannot enroll in a completed course'], 400);
        }

        $enrollment = new Enrollment();
        $enrollment->student_id = $user->id;
        $enrollment->course_id = $course->id;
        $enrollment->isCompleted = false;
        $enrollment->save();

        return response()->json(['message' => 'Enrolled successfully'], 200);
    }

    public function getEnrolledCourses()
    {
        $user = Auth::user();
        $enrollments = Enrollment::where('student_id', $user->id)
            ->with(['course' => function ($query) {
                $query->with(['instructor', 'category', 'chapters', 'ratings']);
            }])
            ->get();
    
        $enrollments = $enrollments->map(function ($enrollment) {
            $course = $enrollment->course;
            return [
                'course' => [
                    'id' => $course->id,
                    'name' => $course->name,
                    'description' => $course->desc,
                    'instructor_name' => $course->instructor->name,
                    'category_name' => $course->category->name,
                    'average_rating' => $course->ratings->avg('rating') ?? 0,
                ],
            ];
        });
    
        return response()->json($enrollments, 200);
    }
}