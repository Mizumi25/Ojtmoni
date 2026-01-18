<?php

namespace App\Livewire\Coordinator;

use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class UserManagement extends Component
{
    public $users = [];
    public $selectedUser = null;
    public $openCreate = false;
    public $name;
    public $email;
    public $password;
    public $role = 'student';
    public $search = '';
    public $availableCourses;
    public $newStudentCourseId;
    public $activeFilter = 'approved'; // Set default filter to 'approved'

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8',
        'role' => 'required|in:student,intern',
        'newStudentCourseId' => 'required|exists:courses,id',
    ];

    public function mount()
    {
        $this->loadStudents($this->activeFilter); // Load approved students by default
        $this->availableCourses = Course::all();
        $coordinator = Auth::user();
        if ($coordinator && $coordinator->role === 'coordinator') {
            $this->newStudentCourseId = $coordinator->course_id;
        }
    }

    public function loadStudents($status = 'approved') // Default to 'approved' if no status is passed
    {
        $this->activeFilter = $status;
        $coordinator = Auth::user();
        if ($coordinator && $coordinator->role === 'coordinator' && $coordinator->course_id) {
            $query = User::where('role', 'student')
                ->where('course_id', $coordinator->course_id)
                ->withTrashed(); // Include soft-deleted users

            if ($status) {
                if ($status === 'intern') {
                    $query->where('status', 'ongoing');
                } elseif ($status === 'rejected') {
                    $query->whereNotNull('deleted_at');
                } else {
                    $query->where('status', $status)
                        ->whereNull('deleted_at'); // Exclude soft-deleted for other statuses
                }
            } else {
                $query->where('status', 'approved') // Default to approved if no status
                    ->whereNull('deleted_at');
            }

            $this->users = $query->get();
        } else {
            $this->users = collect();
        }
        $this->selectedUser = null;
        $this->openCreate = false;
        $this->search = ''; // Reset search when loading new filter
    }

    public function createUser()
    {
        $this->validate();

        $coordinator = Auth::user();
        if (!$coordinator || $coordinator->role !== 'coordinator' || !$coordinator->course_id) {
            session()->flash('error', 'You are not authorized to create students for this course.');
            return;
        }

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
            'status' => 'pending',
            'course_id' => $this->newStudentCourseId,
        ]);

        $this->reset(['name', 'email', 'password', 'role', 'newStudentCourseId']);
        $this->openCreate = false;
        $this->loadStudents($this->activeFilter); // Reload current filter
        session()->flash('message', 'Student created successfully.');
    }

    public function acceptUser($userId)
    {
        $user = User::withTrashed()->findOrFail($userId);
        if ($user->course_id === Auth::user()->course_id && $user->role === 'student' && $user->deleted_at === null) {
            $user->update(['status' => 'approved']);
            $this->loadStudents($this->activeFilter); // Reload current filter
            session()->flash('message', 'Student approved successfully.');
        } else {
            session()->flash('error', 'You are not authorized to approve this student.');
        }
    }

    public function rejectUser($userId)
    {
        $user = User::findOrFail($userId);
        if ($user->course_id === Auth::user()->course_id && $user->role === 'student') {
            $user->delete(); // Use soft delete
            $this->loadStudents($this->activeFilter); // Reload current filter
            session()->flash('message', 'Student rejected successfully.');
        } else {
            session()->flash('error', 'You are not authorized to reject this student.');
        }
    }

    public function restoreUser($userId)
    {
        $user = User::withTrashed()->findOrFail($userId);
        if ($user->course_id === Auth::user()->course_id && $user->role === 'student' && $user->deleted_at !== null) {
            $user->restore();
            $this->loadStudents($this->activeFilter); // Reload current filter
            session()->flash('message', 'Student restored successfully.');
        } else {
            session()->flash('error', 'You are not authorized to restore this student.');
        }
    }

    public function deleteUser($userId)
    {
        $user = User::withTrashed()->findOrFail($userId);
        if ($user->course_id === Auth::user()->course_id && $user->role === 'student' && $user->deleted_at !== null) {
            $user->forceDelete(); // Permanent delete for rejected users
            $this->loadStudents($this->activeFilter); // Reload current filter
            session()->flash('message', 'Student permanently deleted.');
        } elseif ($user->course_id === Auth::user()->course_id && $user->role === 'student' && $user->deleted_at === null && $user->status !== 'pending') {
            $user->forceDelete(); // Permanent delete for non-pending, non-rejected users
            $this->loadStudents($this->activeFilter); // Reload current filter
            session()->flash('message', 'Student permanently deleted.');
        } else {
            session()->flash('error', 'You are not authorized to delete this student.');
        }
    }

    public function render()
    {
        return view('livewire.coordinator.user-management', [
            'courses' => $this->availableCourses,
        ])
            ->layout('layouts.auth-layout');
    }
}