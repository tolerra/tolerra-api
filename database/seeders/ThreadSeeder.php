<?php

namespace Database\Seeders;

use App\Models\Thread;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class ThreadSeeder extends Seeder
{
    public function run()
    {
        $categories = Category::limit(5)->get();
        $users = User::limit(5)->get();

        foreach ($users as $user) {
            Thread::create([
                'category_id' => $categories->random()->id,
                'user_id' => $user->id,
                'title' => 'Sample Thread Title',
                'content' => 'This is a thread content',
            ]);
        }
    }
}