<?php

namespace Database\Seeders;

use App\Models\Progress;
use App\Models\Enrollment;
use App\Models\Chapter;
use Illuminate\Database\Seeder;

class ProgressSeeder extends Seeder
{
    public function run()
    {
        $enrollments = Enrollment::limit(5)->get();
        $chapters = Chapter::limit(5)->get();

        foreach ($enrollments as $enrollment) {
            Progress::create([
                'enrollment_id' => $enrollment->id,
                'chapter_id' => $chapters->random()->id,
                'isFinish' => rand(0, 1),
            ]);
        }
    }
}
