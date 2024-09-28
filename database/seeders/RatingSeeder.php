<?php

namespace Database\Seeders;

use App\Models\Rating;
use App\Models\User;
use App\Models\Course;
use Illuminate\Database\Seeder;

class RatingSeeder extends Seeder
{
    public function run()
    {
        $students = User::where('role', 'student')->limit(5)->get();
        $courses = Course::limit(5)->get();

        foreach ($students as $student) {
            Rating::create([
                'student_id' => $student->id,
                'course_id' => $courses->random()->id,
                'rating' => rand(1, 5),
                'review' => 'This is a review for the course',
            ]);
        }
    }
}
