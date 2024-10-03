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
        $user = Auth::user();
        if (!$user || $user->role !== 'instructor') {
            return response()->json([
                'message' => 'Unauthorized. Only instructors can access this endpoint.'
            ], 403);
        }
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
        ]);

        return response()->json([
            'message' => 'Chapter created successfully',
            'chapter' => $chapter
        ], 201);
    }

public function updateChapter(Request $request, $course_id, $chapter_id)
{
    $user = Auth::user();
    if (!$user || $user->role !== 'instructor') {
        return response()->json([
            'message' => 'Unauthorized. Only instructors can access this endpoint.'
        ], 403);
    }
    // Validasi bahwa user adalah instructor dari course ini
    $course = Course::findOrFail($course_id);
    if ($course->instructor_id !== Auth::id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    // Validasi input
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'file' => 'file|mimes:pdf,doc,docx,ppt,pptx,mp4,mov,avi|max:102400', // 100MB max
        'text' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    // Find the chapter
    $chapter = Chapter::where('course_id', $course_id)->findOrFail($chapter_id);

    // Handle file upload
    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('public/course_contents', $fileName);
        $chapter->file = $filePath;
    }

    // Update chapter details
    $chapter->name = $request->name;
    $chapter->slug = Str::slug($request->name);
    $chapter->text = $request->text;
    $chapter->save();

    return response()->json([
        'message' => 'Chapter updated successfully',
        'chapter' => $chapter
    ], 200);
}

public function deleteChapter($course_id, $chapter_id)
{
    $user = Auth::user();
    if (!$user || $user->role !== 'instructor') {
        return response()->json([
            'message' => 'Unauthorized. Only instructors can access this endpoint.'
        ], 403);
    }
    $course = Course::findOrFail($course_id);
    if ($course->instructor_id !== Auth::id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $chapter = Chapter::where('course_id', $course_id)->findOrFail($chapter_id);

    $chapter->delete();

    return response()->json([
        'message' => 'Chapter deleted successfully'
    ], 200);
}
    

public function getChapterDetail($course_id, $chapter_id)
{
    $course = Course::findOrFail($course_id);

    $chapter = Chapter::where('course_id', $course_id)->where('id', $chapter_id)->firstOrFail();

    $chapters = Chapter::where('course_id', $course_id)->get();

    $formattedChapters = $chapters->map(function ($chapter) {
        return [
            'id' => $chapter->id,
            'name' => $chapter->name,
            'file' => $chapter->file ? env('APP_URL') . '/storage/course_contents/' . basename($chapter->file) : null,
            'text' => $chapter->text,
        ];
    });

    // Kembalikan data dalam format JSON
    return response()->json([
        'message' => 'Successfully retrieved chapter details and list',
        'chapter' => [
            'id' => $chapter->id,
            'name' => $chapter->name,
            'file' => $chapter->file ? env('APP_URL') . '/storage/course_contents/' . basename($chapter->file) : null,
            'text' => $chapter->text,
        ], // Chapter yang sedang dibuka
        'chapters' => $formattedChapters // List semua chapter
    ], 200);
}
}
