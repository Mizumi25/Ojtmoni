<?php

namespace App\Livewire\Coordinator;

use App\Models\User;
use App\Models\Course;
use App\Models\Agency;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Str;

class Assignment extends Component
{
    public $enrolledStudents = [];
    public $nonEnrolledStudents = [];
    public $selectedStudentId; // To hold the ID of the student being assigned
    public $selectedStudent;   // To hold the name of the selected student for display
    public $selectedHours = '';
    public $courseHoursDecimal = 0;
    public $search = '';
    public $agency_id;
    public $agencies = [];
    public $showAssignPanel = false;

    public function mount()
    {
        $coordinator = Auth::user();
        $courseId = $coordinator->course_id;

        // Load all students in this course with their agency
        $students = User::where('role', 'student')
            ->where('course_id', $courseId)
            ->with('agency')
            ->get();

        $this->enrolledStudents = $students->filter(fn($s) => $s->agency_id !== null);
        $this->nonEnrolledStudents = $students->filter(fn($s) => $s->agency_id === null);

        // Load all agencies
        $this->agencies = Agency::orderBy('agency_name')->get();

        // Load total hours for course
        $course = Course::find($courseId);
        $this->courseHoursDecimal = $course->total_hours;

        // Convert to HH:MM format
        $this->selectedHours = $this->decimalToTime($this->courseHoursDecimal);
    }

    public function showAssignAgencyPanel($studentId)
    {
        $this->selectedStudentId = $studentId;
        $this->showAssignPanel = true;
        // Load the student's name for display
        $student = User::find($studentId);
        $this->selectedStudent = $student ? $student->name : null;
    }

    public function assignAgency()
    {
        $this->validate([
            'agency_id' => 'required|exists:agencies,id',
            'selectedStudentId' => 'required|exists:users,id',
        ]);
    
        $agency = Agency::findOrFail($this->agency_id);
    
        // Check if slot is available
        if ($agency->slot === null || $agency->slot <= 0) {
            session()->flash('error', 'Selected agency has no available slots.');
            return;
        }
    
        // Assign student to agency
        $user = User::findOrFail($this->selectedStudentId);
        $user->agency_id = $agency->id;
        $user->status = 'intern'; 
        $user->save();
    
        // Decrement agency slot
        $agency->slot -= 1;
        $agency->save();
    
        // Reset and refresh
        $this->showAssignPanel = false;
        $this->reset('agency_id', 'selectedStudentId', 'selectedStudent');
        $this->mount();
        session()->flash('success', 'Agency assigned successfully.');
    }


    private function decimalToTime($decimal)
    {
        $hours = floor($decimal);
        $minutes = round(($decimal - $hours) * 60);
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    private function timeToDecimal($time)
    {
        if (preg_match('/^(\d{1,3}):(\d{2})$/', $time, $matches)) {
            return (int)$matches[1] + ((int)$matches[2] / 60);
        }
        return null;
    }


    public function saveSelectedHours()
    {
        $decimal = $this->timeToDecimal($this->selectedHours);
        if ($decimal !== null) {
            $coordinator = Auth::user();
            $course = Course::find($coordinator->course_id);
            $course->total_hours = $decimal;
            $course->save();
            $this->updateRemainingHours($course);
            $this->courseHoursDecimal = $decimal;
        }
    }
    
    public function updateRemainingHours(Course $course)
    {
        // Get all students with role 'student' and status 'intern' in the same course
        $students = User::where('course_id', $course->id)
                        ->where('role', 'student')
                        ->where('status', 'intern')
                        ->get();
        
        // Loop through these students and set their remaining_hours to the course's total_hours
        foreach ($students as $student) {
            $student->remaining_hours = $course->total_hours;
            $student->save();
        }
    }



    public function render()
    {
        $filteredEnrolledStudents = $this->enrolledStudents->filter(function ($student) {
            return Str::contains(strtolower($student->name . ' ' . $student->id . ' ' . $student->phone_number), strtolower($this->search));
        });

        $filteredNonEnrolledStudents = $this->nonEnrolledStudents->filter(function ($student) {
            return Str::contains(strtolower($student->name . ' ' . $student->id . ' ' . $student->phone_number), strtolower($this->search));
        });

        return view('livewire.coordinator.assignment', [
            'enrolledStudents' => $filteredEnrolledStudents,
            'nonEnrolledStudents' => $filteredNonEnrolledStudents,
        ])->layout('layouts.auth-layout');
    }
}