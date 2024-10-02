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