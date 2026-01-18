<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Course;
use App\Models\YearLevel;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;

class StudentManagement extends Component
{
    use WithFileUploads;

    public $students;
    public $availableCourses;
    public $yearLevels;
    public $selectedStudentId;
    public $state = [
        'name' => '',
        'email' => '',
        'student_id' => '',
        'course_id' => null,
        'year_level_id' => null,
        'phone_number' => '',
        'profile_picture' => null,
    ];
    public $isAdding = true;
    public $isOpen = false;
    public $confirmingDeleteId;

    public function mount()
    {
        $this->loadStudents();
        $this->loadAvailableCourses();
        $this->loadYearLevels();
    }

    public function loadStudents()
    {
        $this->students = User::where('role', 'student')->with(['course', 'yearLevel'])->get();
    }

    public function loadAvailableCourses()
    {
        $this->availableCourses = Course::all();
    }

    public function loadYearLevels()
    {
        $this->yearLevels = YearLevel::all();
    }

    public function resetState()
    {
        $this->state = [
            'name' => '',
            'email' => '',
            'student_id' => '',
            'course_id' => null,
            'year_level_id' => null,
            'phone_number' => '',
            'profile_picture' => null,
        ];
        $this->selectedStudentId = null;
        $this->isAdding = true;
        $this->resetErrorBag();
    }

    public function openModalForAdd()
    {
        $this->resetState();
        $this->isOpen = true;
    }

    public function openModalForEdit(User $student)
    {
        $this->resetState();
        $this->selectedStudentId = $student->id;
        $this->state = [
            'name' => $student->name,
            'email' => $student->email,
            'student_id' => $student->student_id,
            'course_id' => $student->course_id,
            'year_level_id' => $student->year_level_id,
            'phone_number' => $student->phone_number,
            'profile_picture' => null,
        ];
        $this->isAdding = false;
        $this->isOpen = true;
    }

    public function saveStudent()
{
    $rules = [
        'state.name' => 'required|string|max:255',
        'state.student_id' => 'required|string|max:50|unique:users,student_id',
        'state.course_id' => 'required|exists:courses,id',
        'state.year_level_id' => 'required|exists:year_levels,id',
        'state.phone_number' => [
            'nullable',
            'string',
            'max:20',
            'regex:/^(\+63|0)[0-9]{10,15}$/'
        ],
        'state.profile_picture' => 'nullable|image|max:2048',
    ];

    $messages = [
        'state.phone_number.regex' => 'Please enter a valid Philippine phone number (e.g., +639123456789 or 09123456789)',
    ];

        $this->validate($rules, $messages);

        try {
            $baseEmail = strtolower(str_replace(' ', '', $this->state['name'])) . rand(100, 999);
            $email = $baseEmail . '@gcc.com';
            $password = $baseEmail . '@123';

            $studentData = [
                'name' => $this->state['name'],
                'email' => $email,
                'password' => bcrypt($password),
                'role' => 'student',
                'student_id' => $this->state['student_id'],
                'course_id' => $this->state['course_id'],
                'year_level_id' => $this->state['year_level_id'],
                'phone_number' => $this->state['phone_number'],
            ];

            if ($this->state['profile_picture']) {
                $studentData['profile_picture'] = $this->state['profile_picture']->store('profile-pictures', 'public');
            }

            User::create($studentData);

            session()->flash('message', 'Student added successfully!');
            $this->resetState();
            $this->loadStudents();
            $this->dispatch('close-modal');

        } catch (\Exception $e) {
            session()->flash('error', 'Error creating student: ' . $e->getMessage());
        }
    }

    public function updateStudent()
{
    $rules = [
        'selectedStudentId' => 'required|exists:users,id',
        'state.name' => 'required|string|max:255',
        'state.student_id' => [
            'required',
            'string',
            'max:50',
            Rule::unique('users', 'student_id')->ignore($this->selectedStudentId),
        ],
        'state.course_id' => 'required|exists:courses,id',
        'state.year_level_id' => 'required|exists:year_levels,id',
        'state.phone_number' => [
            'nullable',
            'string',
            'max:20',
            'regex:/^(\+63|0)[0-9]{10,15}$/'
        ],
        'state.profile_picture' => 'nullable|image|max:2048',
    ];

    $messages = [
        'state.phone_number.regex' => 'Please enter a valid Philippine phone number (e.g., +639123456789 or 09123456789)',
    ];
        $this->validate($rules, $messages);

        try {
            $student = User::findOrFail($this->selectedStudentId);

            $dataToUpdate = [
                'name' => $this->state['name'],
                'student_id' => $this->state['student_id'],
                'course_id' => $this->state['course_id'],
                'year_level_id' => $this->state['year_level_id'],
                'phone_number' => $this->state['phone_number'],
            ];

            if ($this->state['profile_picture']) {
                $dataToUpdate['profile_picture'] = $this->state['profile_picture']->store('profile-pictures', 'public');
            }

            $student->update($dataToUpdate);

            session()->flash('message', 'Student updated successfully!');
            $this->resetState();
            $this->loadStudents();
            $this->dispatch('close-modal');

        } catch (\Exception $e) {
            session()->flash('error', 'Error updating student: ' . $e->getMessage());
        }
    }

    public function confirmDelete(User $student)
    {
        $this->confirmingDeleteId = $student->id;
    }

    public function deleteStudent()
    {
        if ($this->confirmingDeleteId) {
            User::where('id', $this->confirmingDeleteId)->delete();
            session()->flash('message', 'Student deleted.');
            $this->loadStudents();
            $this->confirmingDeleteId = null;
            $this->isOpen = false;
        }
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->confirmingDeleteId = null;
    }

    #[On('student-saved')]
    public function refreshStudents()
    {
        $this->loadStudents();
    }

    public function render()
    {
        return view('livewire.admin.student-management')
            ->layout('layouts.auth-layout');
    }
}