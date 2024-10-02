<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function getRecommendation()
    {
        $courses = Course::with(['instructor', 'category'])
            ->where('isValidated', true)
            ->inRandomOrder()
            ->limit(4)
            ->get()
            ->map(function ($course) {
                return $this->formatCourseData($course);
            });

        return response()->json([
            'message' => "Successfully retrieved recommended courses data",
            'courses' => $courses
        ]);
    }

    public function getCourse(Request $request)
    {
        $user = $request->user();
        $query = Course::with(['instructor', 'category'])->where('isValidated', true);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $courses = $query->get()->map(function ($course) use ($user) {
            if ($user && $user->courses()->where('course_id', $course->id)->exists()) {
                return null;
            }
            return $this->formatCourseData($course);
        })->filter()->values();

        return response()->json([
            'message' => "Successfully retrieved courses",
            'courses' => $courses
        ]);
    }

    public function getCourseDetail($course_id)
    {
        $course = Course::with(['instructor', 'category', 'chapters', 'ratings.student'])
        ->where('isValidated', true)
        ->findOrFail($course_id);

        $formattedCourse = $this->formatCourseData($course);
    
        $formattedCourse['chapters'] = $course->chapters->map(function ($chapter) {
            return [
                'id' => $chapter->id,
                'name' => $chapter->name,
                'file' => $chapter->file,
                'isDone' => $chapter->isDone,
            ];
        });

        $formattedCourse['ratings'] = $course->ratings->map(function ($rating) {
            return [
                'id' => $rating->id,
                'rating' => $rating->rating,
                'review' => $rating->review,
                'student_name' => $rating->student->name,
                'created_at' => $rating->created_at,
            ];
        });

        $formattedCourse['average_rating'] = $course->ratings->avg('rating') ?? 0;

        return response()->json([
            'message' => "Successfully retrieved validated course details",
            'course' => $formattedCourse
        ]);
    }

    public function getAllValidatedCourses()
    {
        $courses = Course::with(['instructor', 'category', 'ratings'])
            ->where('isValidated', true)
            ->get()
            ->map(function ($course) {
                $formattedCourse = $this->formatCourseData($course);
                $formattedCourse['average_rating'] = $course->ratings->avg('rating') ?? 0;
                return $formattedCourse;
            });

        return response()->json([
            'message' => "Successfully retrieved all validated courses",
            'courses' => $courses
        ]);
    }

    public function addCourse(Request $request)
    {
        // Cek apakah user terautentikasi
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'desc' => 'required|string',
            'brief' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle file upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/course_images', $imageName);
        }

        // Create new course
        $course = Course::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'instructor_id' => Auth::id(),
            'desc' => $request->desc,
            'brief' => $request->brief,
            'image' => $imageName ?? null,
            'category_id' => $request->category_id,
            'isValidated' => false,
        ]);

        return response()->json([
            'message' => 'Course created successfully',
            'course' => $course
        ], 201);
    }

    private function formatCourseData($course)
    {
        return [
            'id' => $course->id,
            'name' => $course->name,
            'slug' => $course->slug,
            'isValidated' => $course->isValidated,
            'instructor_id' => $course->instructor_id,
            'instructor_name' => $course->instructor->name,
            'category_id' => $course->category_id,
            'category_name' => $course->category->name,
            'description' => $course->description,
            'brief' => $course->brief,
            'image' => env('APP_URL') . '/images/' . $course->image,
            'created_at' => $course->created_at,
            'updated_at' => $course->updated_at,
        ];
    }
}