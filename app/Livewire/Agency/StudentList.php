<?php

namespace App\Livewire\Agency;

use Livewire\Component;
use App\Models\User;
use App\Models\Evaluation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


class StudentList extends Component
{
    public $students;
    public $selectedStudentId;
    public $selectedStudent;

    // Form data (now as scores 1-5)
    public $demonstrates_professionalism_score = null;
    public $communicates_effectively_score = null;
    public $shows_initiative_and_creativity_score = null;
    public $works_well_with_others_score = null;
    public $completes_tasks_on_time_score = null;
    public $follows_company_policies_score = null;
    public $adapts_to_work_environment_score = null;
    public $technical_skills_score = null;
    public $attendance_score = null;
    public $overall_performance_score = null;
    public $evaluator_comments = null;
    public $signature = null; // Add this property for the signature data

    public function mount()
    {
        $this->students = User::where('role', 'student')
            ->where('status', 'intern') // Add this condition
            ->where('remaining_hours', 0)
            ->with(['course', 'yearLevel'])
            ->get();
    }

    public function updatedSelectedStudentId($studentId)
    {
        $this->selectedStudent = User::find($studentId);
    }

    // Add this function to update overall performance dynamically
    public function updated($propertyName)
    {
        if (in_array($propertyName, [
            'demonstrates_professionalism_score',
            'communicates_effectively_score',
            'shows_initiative_and_creativity_score',
            'works_well_with_others_score',
            'completes_tasks_on_time_score',
            'follows_company_policies_score',
            'adapts_to_work_environment_score',
            'technical_skills_score',
            'attendance_score',
        ])) {
            $this->calculateOverallPerformance();
        }
    }

    private function calculateOverallPerformance()
    {
        $criteriaScores = [
            $this->demonstrates_professionalism_score,
            $this->communicates_effectively_score,
            $this->shows_initiative_and_creativity_score,
            $this->works_well_with_others_score,
            $this->completes_tasks_on_time_score,
            $this->follows_company_policies_score,
            $this->adapts_to_work_environment_score,
        ];

        // Filter out null values before calculating the average
        $validCriteriaScores = array_filter($criteriaScores, function ($score) {
            return $score !== null;
        });

        $criteriaSum = array_sum($validCriteriaScores);
        $criteriaCount = count($validCriteriaScores);

        // Avoid division by zero
        $averageCriteriaScore = $criteriaCount > 0 ? $criteriaSum / $criteriaCount : 0;

        $totalScore = $averageCriteriaScore;
        $totalWeight = 1;

        if ($this->technical_skills_score !== null) {
            $totalScore += $this->technical_skills_score;
            $totalWeight++;
        }
        if ($this->attendance_score !== null) {
            $totalScore += $this->attendance_score;
            $totalWeight++;
        }
        // Avoid division by zero
        $this->overall_performance_score = $totalWeight > 0 ? round(($totalScore / $totalWeight / 5) * 100) : null;
    }


    public function submitEvaluation()
    {
        if (!$this->selectedStudent) {
            return;
        }

        $this->validate([
            'demonstrates_professionalism_score' => 'required|integer|min:1|max:5',
            'communicates_effectively_score' => 'required|integer|min:1|max:5',
            'shows_initiative_and_creativity_score' => 'required|integer|min:1|max:5',
            'works_well_with_others_score' => 'required|integer|min:1|max:5',
            'completes_tasks_on_time_score' => 'required|integer|min:1|max:5',
            'follows_company_policies_score' => 'required|integer|min:1|max:5',
            'adapts_to_work_environment_score' => 'required|integer|min:1|max:5',
            'technical_skills_score' => 'required|integer|min:1|max:5',
            'attendance_score' => 'required|integer|min:1|max:5',
            'overall_performance_score' => 'nullable|integer', // Will be calculated
            'evaluator_comments' => 'nullable|string',
            'signature' => 'nullable|string', // Add validation for signature if needed
        ]);
        
        $image = $this->signature;
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = 'signature_' . time() . '.png';

        Storage::disk('public')->put("signatures/{$imageName}", base64_decode($image));

        $this->calculateOverallPerformance();

        Evaluation::create([
            'student_id' => $this->selectedStudent->id,
            'evaluator_id' => Auth::id(),
            'evaluator_name' => Auth::user()->name,
            'demonstrates_professionalism' => $this->demonstrates_professionalism_score,
            'communicates_effectively' => $this->communicates_effectively_score,
            'shows_initiative_and_creativity' => $this->shows_initiative_and_creativity_score,
            'works_well_with_others' => $this->works_well_with_others_score,
            'completes_tasks_on_time' => $this->completes_tasks_on_time_score,
            'follows_company_policies' => $this->follows_company_policies_score,
            'adapts_to_work_environment' => $this->adapts_to_work_environment_score,
            'technical_skills_score' => $this->technical_skills_score,
            'attendance_score' => $this->attendance_score,
            'overall_performance_score' => $this->overall_performance_score,
            'evaluator_comments' => $this->evaluator_comments,
            'signature' => "signatures/{$imageName}",
        ]);

        // Update the student's status to "completed"
        $this->selectedStudent->update(['status' => 'completed']);

        $this->reset(['selectedStudentId', 'selectedStudent', 'demonstrates_professionalism_score', 'communicates_effectively_score', 'shows_initiative_and_creativity_score', 'works_well_with_others_score', 'completes_tasks_on_time_score', 'follows_company_policies_score', 'adapts_to_work_environment_score', 'technical_skills_score', 'attendance_score', 'overall_performance_score', 'evaluator_comments', 'signature']);
        $this->dispatch('evaluationSubmitted');
        $this->js("open = false");
    }

    public function render()
    {
        return view('livewire.agency.student-list')->layout('layouts.auth-layout');
    }
}