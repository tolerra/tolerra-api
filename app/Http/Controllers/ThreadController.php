<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Thread;

class ThreadController extends Controller
{
    public function getThreads()
    {
        $threads = Thread::with('user:id,name')->get();
        return response()->json($threads);
    }

    public function createThread(Request $request)
    {
        $validatedData = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string',
            'content' => 'required|string',
        ]);

        $thread = Thread::create($validatedData);
        return response()->json($thread, 201);
    }

    public function getThreadDetail($thread_id)
    {   
        $thread = Thread::with(['user:id,name', 'comments.user:id,name'])->findOrFail($thread_id);
        return response()->json($thread);
    }

    public function createComment(Request $request, $thread_id)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);

        $thread = Thread::findOrFail($thread_id);
        $comment = $thread->comments()->create($validatedData);
        return response()->json($comment, 201);
    }
}