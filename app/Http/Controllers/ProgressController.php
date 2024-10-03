<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Progress;
use App\Models\Enrollment;
use App\Models\Chapter;

class ProgressController extends Controller
{
    public function addProgress(Request $request)
    {
        $request->validate([
            'chapter_id' => 'required|exists:chapters,id',
            'enrollment_id' => 'required|exists:enrollments,id',
        ]);

        $progress = Progress::create([
            'chapter_id' => $request->chapter_id,
            'enrollment_id' => $request->enrollment_id,
            'isFinish' => true, // Set default progress to true
        ]);

        return response()->json([
            'message' => 'Progress added successfully',
            'progress' => $progress
        ], 201);
    }

    public function getProgress(Request $request)
    {
        $request->validate([
            'chapter_id' => 'required|exists:chapters,id',
            'enrollment_id' => 'required|exists:enrollments,id',
        ]);

        $progress = Progress::where('chapter_id', $request->chapter_id)
                            ->where('enrollment_id', $request->enrollment_id)
                            ->first();

        return response()->json([
            'isFinish' => $progress ? $progress->isFinish : false
        ]);
    }

    public function getEnrollmentProgress($enrollment_id)
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