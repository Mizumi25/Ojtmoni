<?php

namespace App\Livewire\Coordinator;

use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProgressReport extends Component
{
    public $course;
    public $students;
    public $selectedStudentDetails = null;

    public function mount()
    {
        $coordinator = Auth::user();
        $this->course = Course::with('users')->find($coordinator->course_id);

        if ($this->course) {
            $this->students = $this->course->users()->where('role', 'student')->with('agency')->get();
        } else {
            $this->students = collect();
        }
    }

    public function showStudentDetails($studentId)
    {
        $this->selectedStudentDetails = $this->students->firstWhere('id', $studentId);
    }

    public function render()
    {
        return view('livewire.coordinator.progress-report')
            ->layout('layouts.auth-layout');
    }
}