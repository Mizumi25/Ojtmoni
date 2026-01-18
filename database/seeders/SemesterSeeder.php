<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Semester;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Semester::create([
            'grading_code' => '24-01',
            'grading_description' => 'First Semester',
        ]);

        Semester::create([
            'grading_code' => '24-02',
            'grading_description' => 'Second Semester',
        ]);

        // You can create more semesters or use the factory
        Semester::factory()->count(4)->create();
    }
}
