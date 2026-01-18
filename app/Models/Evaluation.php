<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'evaluator_id',
        'evaluator_name',

        // Checkboxes
        'demonstrates_professionalism',
        'communicates_effectively',
        'shows_initiative_and_creativity',
        'works_well_with_others',
        'completes_tasks_on_time',
        'follows_company_policies',
        'adapts_to_work_environment',

        // Scores
        'technical_skills_score',
        'attendance_score',
        'overall_performance_score',

        // Comment
        'evaluator_comments',
        'signature',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }
}
