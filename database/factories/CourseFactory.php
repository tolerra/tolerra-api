<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'slug' => $this->faker->slug,
            'isValidated' => $this->faker->boolean,
        ];
    }
}