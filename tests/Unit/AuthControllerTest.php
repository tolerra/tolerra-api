<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testLogin()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        $data = [
            'email' => $user->email,
            'password' => 'password',
        ];

        $response = $this->postJson('/api/login', $data);

        $response->assertStatus(200)
                 ->assertJsonFragment(['email' => $user->email]);
    }
}