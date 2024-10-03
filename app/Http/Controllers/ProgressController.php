<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Progress;
use App\Models\Enrollment;

class ProgressController extends Controller
{
    public function createProgress($enrollment_id, $chapter_id, Request $request)
    {
        $request->validate([
            'enrollment_id' => 'required|exists:enrollments,id',
            'chapter_id' => 'required|exists:chapters,id',
        ]);

        $progress = Progress::create([
            'chapter_id' => $chapter_id,
            'enrollment_id' => $enrollment_id,
            'isFinish' => true, 
        ]);

        return response()->json([
            'message' => 'Progress created successfully',
            'progress' => $progress
        ], 201);
    }

    public function checkChapterProgress($enrollment_id, $chapter_id)
    {
        $progress = Progress::where('chapter_id', $chapter_id)
                            ->where('enrollment_id', $enrollment_id)
                            ->first();

        return response()->json([
            'isFinish' => $progress ? $progress->isFinish : false
        ]);
    }

    public function checkCourseProgress($enrollment_id)
    {
        $enrollment = Enrollment::findOrFail($enrollment_id);
        $course = $enrollment->course;
        $chapters = $course->chapters;
        $totalChapters = $chapters->count();
        $completedChapters = Progress::where('enrollment_id', $enrollment_id)
                                     ->where('isFinish', true)
                                     ->count();

        $progressPercentage = $totalChapters > 0 ? $completedChapters / $totalChapters : 0;

        return response()->json([
            'progress' => $progressPercentage
        ]);
    }
}