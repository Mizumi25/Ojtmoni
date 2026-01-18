<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\YearLevel;

class YearLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        YearLevel::create([
            'level' => '2nd year',
        ]);
        YearLevel::create([
            'level' => '3rd year',
        ]);
        YearLevel::create([
            'level' => '4th year',
        ]);
    
      // YearLevel::factory(4)->create();
  }
}
