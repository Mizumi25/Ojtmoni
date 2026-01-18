<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Course;
use App\Models\YearLevel;
use App\Models\Location;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $role = $this->faker->randomElement(['coordinator', 'student']);
        $phoneNumber = '+63' . $this->faker->numerify('##########');

        $location = Location::inRandomOrder()->first();

        if ($role === 'coordinator') {
            // Get a random course that doesn't have a coordinator yet
            $availableCourse = Course::whereDoesntHave('users', function ($query) {
                $query->where('role', 'coordinator');
            })->inRandomOrder()->first();

            if ($availableCourse) {
                return [
                    'name' => $this->faker->name(),
                    'email' => $this->faker->unique()->safeEmail(),
                    'email_verified_at' => now(),
                    'password' => static::$password ??= Hash::make('password'),
                    'remember_token' => Str::random(10),
                    'role' => $role,
                    'course_id' => $availableCourse->id,
                    'phone_number' => $phoneNumber,
                ];
            } else {
                // If no available course for a new coordinator, create a student instead
                $role = 'student';
            }
        }

        if ($role === 'student') {
            return [
                'name' => $this->faker->name(),
                'email' => $this->faker->unique()->safeEmail(),
                'email_verified_at' => now(),
                'password' => static::$password ??= Hash::make('password'),
                'remember_token' => Str::random(10),
                'role' => $role,
                'course_id' => Course::inRandomOrder()->first()->id,
                'year_level_id' => YearLevel::inRandomOrder()->first()->id,
                'phone_number' => $phoneNumber,
                'student_id' => '2' . $this->faker->unique()->numberBetween(100000, 999999),
                'location_id' => $location->id,
                'status' => 'pending',
            ];
        }

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => $role,
            'phone_number' => $phoneNumber,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}