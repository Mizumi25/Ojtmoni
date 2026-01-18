<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Course::create([
            'abbreviation' => 'BSIT',
            'full_name' => 'Bachelor of Science in Information Technology',
        ]);
    
      Course::factory(2)->create();
    }
}
