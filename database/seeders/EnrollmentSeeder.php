<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\User;
use App\Models\Course;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    public function run()
    {
        $students = User::where('role', 'student')->limit(5)->get();
        $courses = Course::limit(5)->get();

        foreach ($students as $student) {
            Enrollment::create([
                'student_id' => $student->id,
                'course_id' => $courses->random()->id,
            ]);
        }
    }
}
