<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class CourseController extends Controller
{
    public function getRecommendation()
    {
        $courses = Course::with(['instructor', 'category'])
            ->limit(4)
            ->get()
            ->map(function ($course) {
                return $this->formatCourseData($course);
            });

        return response()->json([
            'message' => "Successfully get recommended courses data",
            'courses' => $courses
        ]);
    }

    public function getCourse(Request $request)
    {
        $user = $request->user();
        $query = Course::with(['instructor', 'category']);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $courses = $query->get()->map(function ($course) use ($user) {
            if ($user && $user->courses->contains($course->id)) {
                return null;
            }
            return $this->formatCourseData($course);
        })->filter();

        return response()->json([
            'message' => "Successfully retrieved courses",
            'courses' => $courses->values()
        ]);
    }

    public function getCourseDetail($course_id)
    {
        $course = Course::with(['instructor', 'category', 'lessons', 'ratings.student'])
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
                'created_at' => $rating->createdAt,
            ];
        });
    
        $formattedCourse['average_rating'] = $course->ratings->avg('rating');
    
        return response()->json([
            'message' => "Successfully retrieved course details",
            'course' => $formattedCourse
        ]);
    }

    /**
     * Helper function to format course data.
     */
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
            'image' => env('BASE_URL') . 'images/' . $course->image,
            'created_at' => $course->created_at,
            'updated_at' => $course->updated_at,
        ];
    }
}