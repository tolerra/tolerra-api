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

    public function addRating(Request $request)
    {
        //
    }

}
