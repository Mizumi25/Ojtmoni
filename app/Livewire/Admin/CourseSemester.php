<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Course;
use App\Models\YearLevel; 
use App\Models\Semester;
use App\Models\User;

class CourseSemester extends Component
{
    public $courses = [];
    public $semesters = [];
    public $selectedCourses = []; // for assigning courses to semester
    public $selectedCourse = null;
    public $selectedSemester = null;
    public $newCourseAbbreviation = '';
    public $newCourseFullName = '';
    public $selectedYearLevels = []; 
    public $yearLevels = [];
    public $newGradingCode = '';
    public $newGradingDescription = '';
    public $newCourseTotalHours = 0;
    public $formattedTotalHours = '';
    
    public $bulkUpdateEnabled = false;
    public $bulkTotalHours = 0;



    public function mount()
    {
        $this->courses = Course::all();
        $this->yearLevels = YearLevel::all(); 
        $this->semesters = Semester::all(); 
    }
    
    public function updateAllCoursesHours()
    {
        $this->validate([
            'bulkTotalHours' => 'required|numeric|min:0',
        ]);
    
        Course::query()->update(['total_hours' => $this->bulkTotalHours]);
        
        User::where('role', 'student')
        ->where('status', 'intern')
        ->update(['remaining_hours' => $this->bulkTotalHours]);
    
        $this->courses = Course::all(); // Refresh
        $this->bulkUpdateEnabled = false;
        $this->bulkTotalHours = 0;
    }
    
    public function setActiveSemester($id)
    {
        // Set all active semesters to upcoming
        Semester::where('status', 'active')->update(['status' => 'upcoming']);
    
        $semester = Semester::find($id);
        if ($semester) {
            // Check if the semester has at least one course offering
            if ($semester->courseOfferings()->count() === 0) {
                session()->flash('error', 'Cannot activate semester without course offerings.');
                return;
            }
    
            $semester->status = 'active';
            $semester->save();
    
            // Update start_date for related course offerings with the current date
            $semester->courseOfferings()->update(['start_date' => now()]);
    
            $this->semesters = Semester::all();
        }
    }


    
    public function markAsCompleted($id)
    {
        $semester = Semester::find($id);
        if ($semester && $semester->status === 'active') {
            $semester->status = 'completed';
            $semester->save();
    
            // Update end_date for related course offerings
            $semester->courseOfferings()->update(['end_date' => now()]);
    
            $this->semesters = Semester::all();
        }
    }






    
    public function getFormattedTotalHoursProperty()
    {
        if ($this->newCourseTotalHours === null) return '00:00:00';
    
        $totalSeconds = round($this->newCourseTotalHours * 3600);
        $h = floor($totalSeconds / 3600);
        $m = floor(($totalSeconds % 3600) / 60);
        $s = $totalSeconds % 60;
    
        return sprintf('%d:%02d:%02d', $h, $m, $s);
    }
    
    public function updatedFormattedTotalHours($value)
    {
        if (preg_match('/^(\d+):(\d{1,2}):(\d{1,2})$/', $value, $matches)) {
            [$full, $h, $m, $s] = $matches;
            $this->newCourseTotalHours = round($h + $m / 60 + $s / 3600, 2);
        } else {
            $this->newCourseTotalHours = 0; 
        }
    }



    public function addCourse()
    {
        $this->validate([
            'newCourseAbbreviation' => 'required|string|max:255',
            'newCourseFullName' => 'required|string|max:255',
            'selectedYearLevels' => 'required|array', 
        ]);
        
        $this->updatedFormattedTotalHours($this->formattedTotalHours);
    
        $course = Course::create([
            'abbreviation' => $this->newCourseAbbreviation,
            'full_name' => $this->newCourseFullName,
            'total_hours' => $this->newCourseTotalHours,
        ]);
    
        // Attach selected year levels to the course
        $course->yearLevels()->attach($this->selectedYearLevels);
    
        $this->resetCourseForm();
        $this->courses = Course::all(); // Refresh the courses list
    }
        
        
    public function updateCourse()
    {
        $this->validate([
            'newCourseAbbreviation' => 'required|string|max:255',
            'newCourseFullName' => 'required|string|max:255',
            'selectedYearLevels' => 'required|array', 
        ]);
        
        $this->updatedFormattedTotalHours($this->formattedTotalHours);
        
        if ($this->selectedCourse) {
            $this->selectedCourse->update([
                'abbreviation' => $this->newCourseAbbreviation,
                'full_name' => $this->newCourseFullName,
                'total_hours' => $this->newCourseTotalHours,
            ]);
            
            User::where('course_id', $this->selectedCourse->id)
              ->where('role', 'student')
              ->where('status', 'intern')
              ->update(['remaining_hours' => $this->newCourseTotalHours]);

    
            $this->selectedCourse->yearLevels()->sync($this->selectedYearLevels);
    
            $this->resetCourseForm();
            $this->courses = Course::all(); // Refresh
        }
    }
    
    
    public function addSemester()
    {
        $this->validate([
            'newGradingCode' => 'required|string|max:255',
            'newGradingDescription' => 'required|string|max:500',
            'selectedCourses' => 'required|array|min:1',
        ]);
    
        $semester = Semester::create([
            'grading_code' => $this->newGradingCode,
            'grading_description' => $this->newGradingDescription,
            'status' => 'upcoming',
        ]);
    
        $semester->courses()->sync($this->selectedCourses); // use pivot
    
        $this->resetSemesterForm();
        $this->semesters = Semester::all();
    }
    
    public function updateSemester()
    {
        $this->validate([
            'newGradingCode' => 'required|string|max:255',
            'newGradingDescription' => 'required|string|max:500',
            'selectedCourses' => 'required|array|min:1',
        ]);
    
        if ($this->selectedSemester) {
            $this->selectedSemester->update([
                'grading_code' => $this->newGradingCode,
                'grading_description' => $this->newGradingDescription,
            ]);
    
            $this->selectedSemester->courses()->sync($this->selectedCourses);
    
            $this->resetSemesterForm();
            $this->semesters = Semester::all();
        }
    }

    
    


    public function selectCourse($id)
    {
        $this->selectedCourse = Course::find($id);
        $this->newCourseAbbreviation = $this->selectedCourse->abbreviation;
        $this->newCourseFullName = $this->selectedCourse->full_name;
        $this->selectedYearLevels = $this->selectedCourse->yearLevels->pluck('id')->toArray();
        $this->newCourseTotalHours = $this->selectedCourse->total_hours;
    
        // Convert to HH:MM:SS
        $totalSeconds = round($this->newCourseTotalHours * 3600);
        $h = floor($totalSeconds / 3600);
        $m = floor(($totalSeconds % 3600) / 60);
        $s = $totalSeconds % 60;
        $this->formattedTotalHours = sprintf('%d:%02d:%02d', $h, $m, $s);
    }

    public function selectSemester($id)
    {
        $this->selectedSemester = Semester::find($id);
        $this->newGradingCode = $this->selectedSemester->grading_code;
        $this->newGradingDescription = $this->selectedSemester->grading_description;
        $this->selectedCourses = $this->selectedSemester->courses()->pluck('courses.id')->toArray();
    }

    
          // Delete Course Method
      public function deleteCourse($id)
      {
          $course = Course::find($id);
          
          if ($course) {
              // Detach any related year levels before deleting the course
              $course->yearLevels()->detach();
              
              // Delete the course
              $course->delete();
              
              // Refresh the course list
              $this->courses = Course::all();
          }
      }
      
      public function deleteSemester($id)
    {
        $semester = Semester::find($id);
    
        if (!$semester) {
            session()->flash('error', 'Semester not found.');
            return;
        }
    
        if (in_array($semester->status, ['active', 'completed'])) {
            session()->flash('error', 'Cannot delete an active or completed semester.');
            return;
        }
    
        $semester->delete();
        session()->flash('success', 'Semester deleted successfully.');
        $this->semesters = Semester::all(); // refresh list
    }






    public function resetCourseForm()
    {
        $this->newCourseAbbreviation = '';
        $this->newCourseFullName = '';
        $this->selectedYearLevels = []; 
        $this->selectedCourse = null; 
        $this->newCourseTotalHours = '';
    }
    
    public function resetSemesterForm()
    {
        $this->newGradingCode = '';
        $this->newGradingDescription = '';
        $this->selectedSemester = null; 
    }

    public function render()
    {
        return view('livewire.admin.course-semester', [
            'courses' => $this->courses,
            'yearLevels' => $this->yearLevels, 
            'semesters' => $this->semesters, 
        ])->layout('layouts.auth-layout');
    }
}