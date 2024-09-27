<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rating;

class RatingController extends Controller
{
    public function getTopReview()
    {
        $topReviews = Rating::select('course_id', 'review', 'rating')
            ->with(['student:id,name', 'course:id,name'])
            ->groupBy('course_id')
            ->orderBy('rating', 'desc')
            ->take(3)
            ->get()
            ->map(function ($rating) {
                return [
                    'student_name' => $rating->student->name,
                    'course_name' => $rating->course->name,
                    'review' => $rating->review,
                ];
            });

        return response()->json($topReviews);
    }

    public function addRating(Request $request)
    {
        //
    }

}
