<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Course;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Str;

class CourseControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testGetRecommendation()
    {
        $instructor = User::factory()->create();
        $category = Category::factory()->create();

        for ($i = 0; $i < 5; $i++) {
            Course::factory()->create([
                'name' => 'Sample Course ' . $i,
                'slug' => 'sample-course-' . $i,
                'isValidated' => true,
                'instructor_id' => $instructor->id,
                'desc' => 'Sample course description ' . $i,
                'category_id' => $category->id
            ]);
        }

        $response = $this->getJson('/api/courses/recommendation');

        $response->assertStatus(200)
                 ->assertJsonCount(4, 'courses');
    }

    public function testGetCourse()
    {
        $instructor = User::factory()->create();
        $category = Category::factory()->create();

        for ($i = 0; $i < 3; $i++) {
            Course::factory()->create([
                'name' => 'Sample Course ' . $i,
                'slug' => 'sample-course-' . $i,
                'isValidated' => true,
                'instructor_id' => $instructor->id,
                'desc' => 'Sample course description ' . $i,
                'category_id' => $category->id
            ]);
        }

        $response = $this->getJson('/api/courses');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'courses');
    }

    public function testGetCourseDetail()
    {
        $instructor = User::factory()->create();
        $category = Category::factory()->create();
        $course = Course::factory()->create([
            'name' => 'Sample Course',
            'slug' => 'sample-course-' . Str::random(8),
            'isValidated' => true,
            'instructor_id' => $instructor->id,
            'desc' => 'Sample course description',
            'category_id' => $category->id
        ]);

        $response = $this->getJson("/api/courses/{$course->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $course->id]);
    }
}