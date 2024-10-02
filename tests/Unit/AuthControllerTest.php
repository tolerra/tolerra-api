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
    public function testRegister()
    {
        Storage::fake('public');
    
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'student',
            'disability_card' => UploadedFile::fake()->image('disability_card.jpg')
        ];
    
        $response = $this->postJson('/api/register/student', $data);
    
        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'Test User']);
    
        Storage::disk('public')->assertExists('disability_cards/' . $data['disability_card']->hashName());
    }

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