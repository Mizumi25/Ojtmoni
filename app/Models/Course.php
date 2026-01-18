<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    /** @use HasFactory<\Database\Factories\CourseFactory> */
    use HasFactory;
    
    protected $fillable = [
        'abbreviation',
        'full_name',
        'total_hours',
    ];
    
    public function users()
    {
        return $this->hasMany(User::class);
    }
    
    public function yearLevels()
    {
        return $this->belongsToMany(YearLevel::class, 'course_year_level'); 
    }
    
    public function semesters()
    {
        return $this->belongsToMany(Semester::class, 'course_offerings');
    }
}
