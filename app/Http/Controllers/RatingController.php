<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rating;

class RatingController extends Controller
{
    public function getTopReviews()
    {
        $topReviews = Rating::select('course_id', 'student_id', 'review')
            ->with(['course:id,name', 'student:id,name'])
            ->distinct('course_id')
            ->take(3)
            ->get();

        return response()->json($topReviews);
    }

    public function addRating(Request $request, $course_id)
    {
        $validatedData = $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
            'review' => 'nullable|string',
        ]);

        $rating = Rating::create([
            'student_id' => $request->user()->id,
            'course_id' => $course_id,
            'rating' => $validatedData['rating'],
            'review' => $validatedData['review'],
        ]);

        return response()->json($rating, 201);
    }
}