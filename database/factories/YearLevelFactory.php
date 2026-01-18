<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\YearLevel;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\YearLevel>
 */
class YearLevelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = YearLevel::class;

    public function definition(): array
    {
        return [
            'level' => $this->faker->randomElement(['1st Year', '2nd Year', '3rd Year', '4th Year']),
        ];
    }
}
