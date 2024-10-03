<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Log;

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
        
        if ($user && $user->role === 'admin') {
            $query = Course::with(['instructor', 'category'])->where('isValidated', false);
        } elseif ($user && $user->role === 'instructor') {
            $query = Course::with(['instructor', 'category'])->where('instructor_id', $user->id);
        } else {
            $query = Course::with(['instructor', 'category'])->where('isValidated', true);
        }
    
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }
    
        $courses = $query->get()->map(function ($course) use ($user) {
            return $this->formatCourseData($course);
        });
    
        return response()->json([
            'message' => "Successfully retrieved courses",
            'courses' => $courses
        ]);
    }

    public function getEnrolledDetailCourse($course_id)
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

    public function getCourseDetail($course_id)
    {
        $user = Auth::user();
        $userRole = $user ? $user->role : 'guest';
        if ($userRole === 'admin') {
            $course = Course::with(['instructor', 'category', 'chapters'])
                ->where('isValidated', false)
                ->findOrFail($course_id);
        } else {
            $course = Course::with(['instructor', 'category', 'chapters', 'ratings.student'])
                ->where('isValidated', true)
                ->findOrFail($course_id);
        }

        $formattedCourse = $this->formatCourseData($course);

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

    public function getInstructorCourses(Request $request)
    {
        // Pastikan user yang terautentikasi adalah seorang instructor
        $user = Auth::user();
        if (!$user || $user->role !== 'instructor') {
            return response()->json([
                'message' => 'Unauthorized. Only instructors can access this endpoint.'
            ], 403);
        }

        $courses = Course::where('id', $user->id)
            ->with(['category', 'ratings'])
            ->get()
            ->map(function ($course) {
                $formattedCourse = $this->formatCourseData($course);
                $formattedCourse['average_rating'] = $course->ratings->avg('rating') ?? 0;
                $formattedCourse['total_students'] = Enrollment::where('course_id', $course->id)->count();
                return $formattedCourse;
            });

        return response()->json([
            'message' => "Successfully retrieved instructor's courses",
            'courses' => $courses
        ]);
    }

    public function addCourse(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'instructor') {
            return response()->json([
                'message' => 'Unauthorized. Only instructors can access this endpoint.'
            ], 403);
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

        // Buat course baru
        $course = Course::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
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

    public function updateCourse(Request $request, $id)
    {
    try {
        $user = Auth::user();
        if (!$user || $user->role !== 'instructor') {
            return response()->json([
                'message' => 'Unauthorized. Only instructors can access this endpoint.'
            ], 403);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'desc' => 'nullable|string',
            'brief' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        // Cek jika validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Temukan course
        $course = Course::findOrFail($id);

        // Data untuk update
        $data = [
            'name' => $request->input('name', $course->name),
            'desc' => $request->input('desc', $course->desc),
            'brief' => $request->input('brief', $course->brief),
            'category_id' => $request->input('category_id', $course->category_id),
        ];

        // Tangani upload file jika ada
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('course_images');
            $image->move($destinationPath, $imageName);
            $data['image'] = $imageName; // Simpan nama file gambar yang baru
        }

        // Update course
        $course->update($data);

        // Update slug
        $course->slug = Str::slug($course->name);
        $course->save();

        return response()->json([
            'message' => 'Course updated successfully',
            'course' => $course
        ], 200);
    } catch (\Exception $e) {
        Log::error('Error updating course: ' . $e->getMessage());
        return response()->json([
            'message' => 'An error occurred while updating the course. Please try again later.'
        ], 500);
    }
}

public function deleteCourse($id)
{
    $user = Auth::user();
    if (!$user || $user->role !== 'instructor') {
        return response()->json([
            'message' => 'Unauthorized. Only instructors can access this endpoint.'
        ], 403);
    }

    $course = Course::findOrFail($id);
    $course->delete();

    return response()->json([
        'message' => 'Course deleted successfully'
    ]);
}


    private function formatCourseData($course)
    {
        return [
            'id' => $course->id,
            'name' => $course->name,
            'slug' => $course->slug,
            'description' => $course->desc,
            'isValidated' => $course->isValidated,
            'instructor_id' => $course->instructor_id,
            'instructor_name' => $course->instructor->name,
            'category_id' => $course->category_id,
            'category_name' => $course->category->name,
            'brief' => $course->brief,
            'image' => env('APP_URL') . '/images/' . $course->image,
            'created_at' => $course->created_at,
            'updated_at' => $course->updated_at,
        ];
    }    
}
