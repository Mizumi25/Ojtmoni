<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    /** @use HasFactory<\Database\Factories\SemesterFactory> */
    use HasFactory;
    
    protected $fillable = [
        'grading_code',
        'grading_description',
        'status',
    ];
    
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_offerings');
    }
    // In Semester.php
    public function courseOfferings()
    {
        return $this->hasMany(CourseOffering::class);
    }

}
