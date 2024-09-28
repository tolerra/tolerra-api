<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $categories = Category::limit(5)->get();
        $instructors = User::where('role', 'instructor')->limit(5)->get();

        foreach ($instructors as $instructor) {
            Course::create([
                'name' => 'Course ' . rand(1, 100),
                'slug' => Str::slug('Course ' . rand(1, 100)),
                'instructor_id' => $instructor->id,
                'desc' => 'This is a course description',
                'brief' => 'This is a brief summary',
                'category_id' => $categories->random()->id,
                'isValidated' => rand(0, 1),
            ]);
        }
    }
}
