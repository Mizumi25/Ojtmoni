<?php

namespace Database\Factories;

use App\Models\Agency;
use App\Models\User;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Agency>
 */
class AgencyFactory extends Factory
{
    protected $model = Agency::class;

    public function definition(): array
    {
        $agencyName = $this->attributes['agency_name'] ?? $this->faker->company;

        $user = User::firstOrCreate(
            ['email' => strtolower(str_replace(' ', '', $agencyName)) . '@example.com'],
            [
                'name' => $agencyName,
                'password' => bcrypt($agencyName . '123'),
                'role' => 'agency',
                'phone_number' => '+63' . $this->faker->numerify('##########'),
            ]
        );

        $location = Location::create([
            'location_name' => $agencyName . ' Branch',
            'longitude' => $this->faker->numberBetween(116000000, 127000000) / 1000000,
            'latitude' => $this->faker->numberBetween(4230000, 21250000) / 1000000,
        ]);

        return [
            'agency_name' => $agencyName,
            'agency_background' => $this->faker->paragraph,
            'contact_person_id' => $user->id,
            'agency_number' => '+63' . $this->faker->numerify('##########'),
            'location_id' => $location->id,
        ];
    }
}
