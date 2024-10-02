<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Thread;
use App\Models\User;

class ThreadControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testGetThreads()
    {
        Thread::factory()->count(3)->create();

        $response = $this->getJson('/api/threads');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }
    
    public function testGetThreadDetail()
    {
        $thread = Thread::factory()->create();

        $response = $this->getJson("/api/threads/{$thread->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $thread->id]);
    }
}