<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            BadgeSeeder::class,
            CategorySeeder::class,
            CourseSeeder::class,
            ChapterSeeder::class,
            EnrollmentSeeder::class,
            ProgressSeeder::class,
            RatingSeeder::class,
            NotificationSeeder::class,
            ThreadSeeder::class,
            CommentSeeder::class,
            DisabilityVerificationSeeder::class,
        ]);
    }
}
