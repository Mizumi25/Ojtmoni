<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\CourseSeeder;
use Database\Seeders\YearLevelSeeder;
use Database\Seeders\SemesterSeeder;
use Database\Seeders\AgencySeeder;
use Database\Seeders\LocationSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void {
        $this->call([
            CourseSeeder::class,
            YearLevelSeeder::class,
            SemesterSeeder::class,
            LocationSeeder::class,
            UserSeeder::class,
            AgencySeeder::class,
        ]);
    }
}
