<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Semester;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Semester>
 */
class SemesterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Semester::class;

    public function definition(): array
    {
        return [
            'grading_code' => $this->faker->unique()->bothify('##-##'), 
            'grading_description' => $this->faker->sentence(3),
        ];
    }
}
