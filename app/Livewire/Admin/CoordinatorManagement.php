<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Course;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;

class CoordinatorManagement extends Component
{
    public $coordinators;
    public $availableCourses;
    public $selectedCoordinatorId;
    public $state = [
        'name' => '',
        'email' => '',
        'course_id' => null,
        'phone_number' => '',
        'password' => '',
    ];
    public $isAdding = true;
    public $isOpen = false;
    public $showPassword = false;
    public $confirmingDeleteId;

    public function mount()
    {
        $this->loadCoordinators();
        $this->loadAvailableCourses();
    }

    public function loadCoordinators()
    {
        $this->coordinators = User::where('role', 'coordinator')->with('course')->get();
    }

    public function loadAvailableCourses($courseId = null)
{
    $this->availableCourses = Course::all();
}

    public function resetState()
{
    $this->state = [
        'name' => '',
        'email' => '',
        'course_id' => null,
        'phone_number' => '',
        'password' => '',
    ];
    $this->selectedCoordinatorId = null;
    $this->isAdding = true;
    $this->resetErrorBag();
    $this->loadAvailableCourses();
}

public function openModalForAdd()
{
    $this->resetState();
    $this->isOpen = true;
}

public function openModalForEdit(User $coordinator)
{
    $this->resetState();
    $this->selectedCoordinatorId = $coordinator->id;
    $this->state = [
        'name' => $coordinator->name,
        'email' => $coordinator->email,
        'course_id' => $coordinator->course_id,
        'phone_number' => $coordinator->phone_number,
        'password' => '',
    ];
    $this->isAdding = false;
    $this->isOpen = true;
    $this->loadAvailableCourses($coordinator->course_id);
}

    public function saveCoordinator()
{
    $rules = [
        'state.name' => 'required|string|max:255',
        'state.course_id' => [
            'required',
            'exists:courses,id',
            Rule::unique('users', 'course_id')->where(function ($query) {
                return $query->where('role', 'coordinator');
            })->ignore($this->selectedCoordinatorId),
        ],
        'state.phone_number' => [
            'nullable', // Changed from required to nullable
            'string',
            'max:20',
            'regex:/^(\+63|0)?[0-9]{10,15}$/' // Fixed regex to make +63 optional
        ],
    ];
    $messages = [
        'state.phone_number.regex' => 'Please enter a valid Philippine phone number (e.g., +639123456789 or 09123456789)',
    ];
    $this->validate($rules, $messages);

    try {
        $baseEmail = strtolower(str_replace(' ', '', $this->state['name'])) . rand(100, 999);
        $email = $baseEmail . '@gcc.com';
        $password = $baseEmail . '@123';

        User::create([
            'name' => $this->state['name'],
            'email' => $email,
            'role' => 'coordinator',
            'password' => bcrypt($password),
            'course_id' => $this->state['course_id'],
            'phone_number' => $this->state['phone_number'],
            'profile_picture' => null,
        ]);

        session()->flash('message', 'Coordinator added successfully!');
        $this->resetState();
        $this->loadCoordinators();
        $this->dispatch('close-modal');

    } catch (\Exception $e) {
        session()->flash('error', 'Error creating coordinator: ' . $e->getMessage());
    }
}

    public function updateCoordinator()
{
    $rules = [
        'selectedCoordinatorId' => 'required|exists:users,id',
        'state.name' => 'required|string|max:255',
        'state.email' => [
            'required',
            'email',
            Rule::unique('users', 'email')->ignore($this->selectedCoordinatorId),
        ],
        'state.course_id' => [
            'required',
            'exists:courses,id',
            Rule::unique('users', 'course_id')->where(function ($query) {
                return $query->where('role', 'coordinator')
                    ->where('id', '!=', $this->selectedCoordinatorId);
            }),
        ],
        'state.phone_number' => [
            'nullable', // Changed from required to nullable
            'string',
            'max:20',
            'regex:/^(\+63|0)?[0-9]{10,15}$/' // Fixed regex to make +63 optional
        ],
        'state.password' => 'nullable|string|min:8',
    ];
    $messages = [
        'state.phone_number.regex' => 'Please enter a valid Philippine phone number (e.g., +639123456789 or 09123456789)',
    ];
        $this->validate($rules, $messages);

        try {
            $email = $this->state['email'];
            if (!str_ends_with($email, '@gcc.com')) {
                $emailParts = explode('@', $email);
                $email = $emailParts[0] . '@gcc.com';
                $this->state['email'] = $email;
            }

            $coordinator = User::findOrFail($this->selectedCoordinatorId);

            $dataToUpdate = [
                'name' => $this->state['name'],
                'email' => $email,
                'course_id' => $this->state['course_id'],
                'phone_number' => $this->state['phone_number'],
            ];

            if (!empty($this->state['password'])) {
                $dataToUpdate['password'] = bcrypt($this->state['password']);
            }

            $coordinator->update($dataToUpdate);

            session()->flash('message', 'Coordinator updated successfully.');
            $this->resetState();
            $this->loadCoordinators();
            $this->dispatch('close-modal');

        } catch (\Exception $e) {
            session()->flash('error', 'Error updating coordinator: ' . $e->getMessage());
        }
    }

    public function confirmDelete(User $coordinator)
    {
        $this->confirmingDeleteId = $coordinator->id;
    }

    public function deleteCoordinator()
    {
        if ($this->confirmingDeleteId) {
            User::where('id', $this->confirmingDeleteId)->delete();
            session()->flash('message', 'Coordinator deleted.');
            $this->loadCoordinators();
            $this->confirmingDeleteId = null;
            $this->isOpen = false;
        }
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->confirmingDeleteId = null;
    }

    #[On('coordinator-saved')]
    public function refreshCoordinators()
    {
        $this->loadCoordinators();
    }

    public function render()
    {
        return view('livewire.admin.coordinator-management')
            ->layout('layouts.auth-layout');
    }
}