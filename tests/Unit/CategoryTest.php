<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Category;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function testCategoryCreation()
    {
        $category = Category::factory()->create([
            'name' => 'Test Category',
        ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
        ]);
    }
}