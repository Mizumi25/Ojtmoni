<?php

// Livewire Component (TrackingMap.php)
namespace App\Livewire;

use Livewire\Component;
use App\Models\Agency;
use App\Models\User;

class TrackingMap extends Component
{
    public $agencies;
    public $allStudents;
    public $mapStudents;
    public $latitude;
    public $longitude;
    public $filter = 'all';

    public function mount($agency_id = null)
    {
        // Load agencies with their contact persons and images
        $this->agencies = Agency::with(['location', 'contactPerson'])
            ->whereHas('location')
            ->get();

        // Get all intern students (for the list)
        $this->allStudents = User::with('location')
            ->where('role', 'student')
            ->where('status', 'intern')
            ->get();

        // Get only map-visible students (for the map)
        $this->mapStudents = User::with('location')
            ->where('role', 'student')
            ->where('status', 'intern')
            ->where('map_exposed', true)
            ->whereNotNull('location_id')
            ->get();

        // Set default or agency-specific coordinates
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
        return view('livewire.tracking-map')
            ->layout('layouts.auth-layout');
    }
}