<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'location_name' => $this->faker->city . ' Center',
            'longitude' => $this->faker->numberBetween(116000000, 127000000) / 1000000,
            'latitude' => $this->faker->numberBetween(4230000, 21250000) / 1000000,
        ];
    }
}
