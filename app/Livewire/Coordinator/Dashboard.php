<?php

namespace App\Livewire\Coordinator;

use App\Models\User;
use App\Models\Agency;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public $users;
    public $latitude;
    public $longitude;
    public $agencies;

    public function mount($agency_id = null)
    {
        $this->agencies = Agency::with('location')->get();
        
        // Get the logged-in user's course_id (assuming it's stored in the 'course_id' column)
        $courseId = Auth::user()->course_id;

        // Filter users (students) by role and course_id
        $this->users = User::where('role', 'student')
                           ->where('course_id', $courseId) // Filter by course_id of logged-in coordinator
                           ->with('location')
                           ->orderByDesc('created_at') // Sort by most recent
                           ->take(6) // Get only the latest 6 students
                           ->get();

        if ($agency_id) {
            $agency = Agency::with('location')->find($agency_id);
            if ($agency && $agency->location) {
                $this->latitude = $agency->location->latitude;
                $this->longitude = $agency->location->longitude;
            }
        } else {
            $this->latitude = 8.8883;
            $this->longitude = 125.1450;
        }
    }

    public function render()
    {
        return view('livewire.coordinator.dashboard', [
            'agencies' => $this->agencies,
            'users' => $this->users, // Pass users to the view
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ])
            ->layout('layouts.auth-layout');
    }
}
