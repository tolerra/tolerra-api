<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class CourseController extends Controller
{
    public function getRecommendation()
    {
        $courses = Course::with(['instructor', 'category'])
            ->where('isValidated', true)
            ->inRandomOrder()
            ->limit(4)
            ->get()
            ->map(function ($course) {
                return $this->formatCourseData($course);
            });

        return response()->json([
            'message' => "Successfully retrieved recommended courses data",
            'courses' => $courses
        ]);
    }

    public function getCourse(Request $request)
    {
        $user = $request->user();
        $query = Course::with(['instructor', 'category'])->where('isValidated', true);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $courses = $query->get()->map(function ($course) use ($user) {
            if ($user && $user->courses()->where('course_id', $course->id)->exists()) {
                return null;
            }
            return $this->formatCourseData($course);
        })->filter()->values();

        return response()->json([
            'message' => "Successfully retrieved courses",
            'courses' => $courses
        ]);
    }

    public function getCourseDetail($course_id)
    {
        $course = Course::with(['instructor', 'category', 'lessons', 'ratings.student'])
            ->where('isValidated', true)
            ->findOrFail($course_id);

        $formattedCourse = $this->formatCourseData($course);
        
        $formattedCourse['lessons'] = $course->lessons->map(function ($lesson) {
            return [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'duration' => $lesson->duration,
            ];
        });

        $formattedCourse['ratings'] = $course->ratings->map(function ($rating) {
            return [
                'id' => $rating->id,
                'rating' => $rating->rating,
                'review' => $rating->review,
                'student_name' => $rating->student->name,
                'created_at' => $rating->created_at,
            ];
        });

        $formattedCourse['average_rating'] = $course->ratings->avg('rating') ?? 0;

        return response()->json([
            'message' => "Successfully retrieved validated course details",
            'course' => $formattedCourse
        ]);
    }

    public function getAllValidatedCourses()
    {
        $courses = Course::with(['instructor', 'category', 'ratings'])
            ->where('isValidated', true)
            ->get()
            ->map(function ($course) {
                $formattedCourse = $this->formatCourseData($course);
                $formattedCourse['average_rating'] = $course->ratings->avg('rating') ?? 0;
                return $formattedCourse;
            });

        return response()->json([
            'message' => "Successfully retrieved all validated courses",
            'courses' => $courses
        ]);
    }

    private function formatCourseData($course)
    {
        return [
            'id' => $course->id,
            'name' => $course->name,
            'slug' => $course->slug,
            'instructor_id' => $course->instructor_id,
            'instructor_name' => $course->instructor->name,
            'category_id' => $course->category_id,
            'category_name' => $course->category->name,
            'description' => $course->description,
            'brief' => $course->brief,
            'image' => env('APP_URL') . '/images/' . $course->image,
            'created_at' => $course->created_at,
            'updated_at' => $course->updated_at,
        ];
    }
}