<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Thread;

class ThreadTest extends TestCase
{
    use RefreshDatabase;

    public function testThreadCreation()
    {
        $thread = Thread::factory()->create([
            'title' => 'Test Thread',
            'content' => 'This is a test thread.',
        ]);

        $this->assertDatabaseHas('threads', [
            'title' => 'Test Thread',
            'content' => 'This is a test thread.',
        ]);
    }
}