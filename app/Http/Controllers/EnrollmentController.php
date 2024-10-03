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

        // Count the number of active enrollments where isCompleted is false
        $activeEnrollments = Enrollment::where('student_id', $user->id)
            ->whereHas('course', function ($query) {
                $query->where('isCompleted', false);
            })
            ->count();

        // If the user has more than 2 active enrollments, disallow enrollment
        if ($activeEnrollments >= 2) {
            return response()->json(['message' => 'Cannot enroll in more than 2 active courses'], 400);
        }

        if ($course->isCompleted) {
            return response()->json(['message' => 'Cannot enroll in a completed course'], 400);
        }

        $enrollment = new Enrollment();
        $enrollment->student_id = $user->id;
        $enrollment->course_id = $course->id;
        $enrollment->save();

        return response()->json(['message' => 'Enrolled successfully'], 200);
    }
    public function getEnrolledCourses()
    {
        $user = Auth::user();
        $enrollments = Enrollment::where('student_id', $user->id)->with('course')->get();

        return response()->json($enrollments, 200);
    }
}