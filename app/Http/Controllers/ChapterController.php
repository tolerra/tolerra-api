<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Chapter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ChapterController extends Controller
{
    public function addChapter(Request $request, $course_id)
    {
        // Validasi bahwa user adalah instructor dari course ini
        $course = Course::findOrFail($course_id);
        if ($course->instructor_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,mp4,mov,avi|max:102400', // 100MB max
            'text' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('public/course_contents', $fileName);
        }

        // Create new chapter
        $chapter = Chapter::create([
            'course_id' => $course_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'file' => $filePath ?? null,
            'text' => $request->text,
            'isDone' => false,
        ]);

        return response()->json([
            'message' => 'Chapter created successfully',
            'chapter' => $chapter
        ], 201);
    }
    public function getChapterDetail($course_id, $chapter_id)
    {
        // Cek apakah course ada
        $course = Course::findOrFail($course_id);

        // Ambil detail chapter berdasarkan ID
        $chapter = Chapter::where('course_id', $course->id)->findOrFail($chapter_id);

        // Format data chapter
        $formattedChapter = [
            'id' => $chapter->id,
            'name' => $chapter->name,
            'file' => $chapter->file ? env('APP_URL') . '/storage/course_contents/' . basename($chapter->file) : null, // Menyediakan URL file jika ada
            'text' => $chapter->text,
            'isDone' => $chapter->isDone,
        ];

        return response()->json([
            'message' => 'Successfully retrieved chapter details',
            'chapter' => $formattedChapter
        ], 200);
    }
}
