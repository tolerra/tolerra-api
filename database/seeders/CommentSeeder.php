<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Thread;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    public function run()
    {
        $threads = Thread::limit(5)->get();

        foreach ($threads as $thread) {
            Comment::create([
                'thread_id' => $thread->id,
                'name' => 'Seeder User',
                'content' => 'This is a comment on the thread',
            ]);
        }
    }
}