<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YearLevel extends Model
{
    /** @use HasFactory<\Database\Factories\YearLevelFactory> */
    use HasFactory;
    
    protected $fillable = [
        'level',
    ];
    
    public function users()
    {
        return $this->hasMany(User::class);
    }
    
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_year_level'); // Specify the pivot table name
    }
}
