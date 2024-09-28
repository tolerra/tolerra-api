<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\Course;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ChapterSeeder extends Seeder
{
    public function run()
    {
        $courses = Course::limit(5)->get();

        foreach ($courses as $course) {
            for ($i = 0; $i < 5; $i++) {
                Chapter::create([
                    'course_id' => $course->id,
                    'name' => 'Chapter ' . rand(1, 10),
                    'slug' => Str::slug('Chapter ' . rand(1, 10)),
                    'file' => 'chapter_file.pdf',
                    'text' => 'This is the text of the chapter',
                    'isDone' => rand(0, 1),
                ]);
            }
        }
    }
}
