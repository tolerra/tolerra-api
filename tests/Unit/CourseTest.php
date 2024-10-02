<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Course;
use App\Models\User;
use App\Models\Category;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    public function testCourseCreation()
    {
        $instructor = User::factory()->create();
        $category = Category::factory()->create();

        $course = Course::factory()->create([
            'name' => 'Test Course',
            'slug' => 'test-course',
            'instructor_id' => $instructor->id,
            'desc' => 'Test course description',
            'brief' => 'Test course brief',
            'category_id' => $category->id,
            'isValidated' => true,
            'image' => 'test-course-image.jpg'
        ]);

        $this->assertDatabaseHas('courses', [
            'name' => 'Test Course',
            'slug' => 'test-course',
            'instructor_id' => $instructor->id,
            'desc' => 'Test course description',
            'brief' => 'Test course brief',
            'category_id' => $category->id,
            'isValidated' => true,
            'image' => 'test-course-image.jpg'
        ]);
    }
}