<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function recomendation()
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
