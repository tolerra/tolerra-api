<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create one Admin User
        User::create([
            'name' => 'Admin User',
            'role' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create 5 Instructors
        User::factory()->count(5)->create([
            'role' => 'instructor',
        ]);

        // Create 5 Students
        User::factory()->count(5)->create([
            'role' => 'student',
        ]);
    }
}
