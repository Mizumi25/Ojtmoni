<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Location;
use App\Models\Agency;

class AgencySeeder extends Seeder
{
    public function run(): void
    {
        // First create random agencies
        Agency::factory(5)->create();

        // Manually create special agency
        $user = User::firstOrCreate(
            ['email' => 'specialagency@example.com'],
            [
                'name' => 'Special Agency',
                'password' => bcrypt('specialagency123'),
                'role' => 'agency',
                'phone_number' => '+63' . fake()->numerify('##########'),
            ]
        );

        $location = Location::create([
            'location_name' => 'Special Agency Branch',
            'longitude' => fake()->numberBetween(116000000, 127000000) / 1000000,
            'latitude' => fake()->numberBetween(4230000, 21250000) / 1000000,
        ]);

        Agency::create([
            'agency_name' => 'Special Agency',
            'agency_background' => fake()->paragraph,
            'contact_person_id' => $user->id,
            'agency_number' => '09676153305',
            'location_id' => $location->id,
        ]);
    }
}
