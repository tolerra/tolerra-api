<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getRecommendation()
    {
        $courses = Course::with(['instructor', 'category'])
            ->limit(4)
            ->get()
            ->map(function ($course) {
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
                    'average_rating' => number_format($course->averageRating(), 1),
                    'created_at' => $course->created_at,
                    'updated_at' => $course->updated_at,
                ];
            });
        return response()->json([
            'message' => "Successfully get recomended courses data",
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
                'average_rating' => number_format($course->averageRating(), 1),
                'created_at' => $course->created_at,
                'updated_at' => $course->updated_at,
            ];
        })->filter();

        return response()->json([
            'message' => "Successfully retrieved courses",
            'courses' => $courses->values()
        ]);
    }
}
