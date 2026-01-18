<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder {
    public function run() {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'phone_number' => '+63123456789',
        ]);

        User::create([
            'name' => 'Coordinator User',
            'email' => 'coordinator@example.com',
            'password' => Hash::make('coordinator123'),
            'role' => 'coordinator',
            'course_id' => Course::where('abbreviation', 'BSIT')->first()->id ?? Course::inRandomOrder()->first()->id,
            'phone_number' => '+63123456789',
        ]);
        
        
        User::create([
            'name' => 'Student User',
            'email' => 'student@example.com',
            'password' => Hash::make('student123'),
            'role' => 'student',
            'phone_number' => '+63123456789',
            'course_id' => Course::where('abbreviation', 'BSIT')->first()->id ?? Course::inRandomOrder()->first()->id,
            'status' => 'approved',
        ]);
        
        User::create([
            'name' => 'Intern User',
            'email' => 'intern@example.com',
            'password' => Hash::make('intern123'),
            'role' => 'student',
            'phone_number' => '+63123456789',
            'course_id' => Course::where('abbreviation', 'BSIT')->first()->id ?? Course::inRandomOrder()->first()->id,
            'status' => 'intern',
        ]);
        
        User::factory(10)->create();
    }
}
