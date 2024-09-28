<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\User;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run()
    {
        $instructors = User::where('role', 'instructor')->limit(5)->get();

        foreach ($instructors as $instructor) {
            Badge::create([
                'instructor_id' => $instructor->id,
                'milestone' => rand(1, 10),
            ]);
        }
    }
}
