<?php

namespace App\Livewire\Agency;

use Livewire\Component;
use App\Models\Semester;
use App\Models\CourseOffering;
use App\Models\User;
use App\Models\YearLevel;
use Illuminate\Support\Collection;

class ViewSemester extends Component
{
    public Collection $semesters;
    public Collection $courses;
    public Collection $students;
    public Collection $yearLevels;
    public $selectedSemesterId;
    public $selectedCourseId;

    public function mount()
    {
        $this->loadSemesters();
        $this->courses = collect();
        $this->students = collect();
        $this->yearLevels = YearLevel::all(); // Load all year levels
        $this->selectedSemesterId = null;
        $this->selectedCourseId = null;
    }

    public function loadSemesters()
    {
        $this->semesters = Semester::all();
    }

    public function loadCourses($semesterId)
    {
        $this->selectedSemesterId = $semesterId;
        $this->courses = CourseOffering::where('semester_id', $semesterId)
            ->with('course')
            ->get()
            ->pluck('course')
            ->unique();
        $this->students = collect(); // Clear students when loading new courses
        $this->selectedCourseId = null;
        $this->dispatch('courses-loaded');
    }

    public function loadStudents($courseId)
    {
        $this->selectedCourseId = $courseId;

        // --- Debugging Start ---
        \Log::info('Selected Semester ID: ' . $this->selectedSemesterId);
        \Log::info('Selected Course ID: ' . $this->selectedCourseId);

        $courseOfferingIds = CourseOffering::where('semester_id', $this->selectedSemesterId)
            ->where('course_id', $this->selectedCourseId)
            ->pluck('id')
            ->toArray();
        \Log::info('Matching Course Offering IDs: ' . json_encode($courseOfferingIds));

        $this->students = User::where('role', 'student')
            ->where('course_id', $courseId)
            ->whereIn('course_offering_id', $courseOfferingIds)
            ->with('yearLevel', 'course')
            ->get();

        \Log::info('Number of Students Found: ' . $this->students->count());
        foreach ($this->students as $student) {
            \Log::info('Found Student: ' . $student->name . ' (Course Offering ID: ' . $student->course_offering_id . ')');
        }
        // --- Debugging End ---

        $this->dispatch('students-loaded');
    }

    public function render()
    {
        return view('livewire.agency.view-semester')
            ->layout('layouts.auth-layout');
    }
}