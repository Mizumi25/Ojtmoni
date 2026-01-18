<?php

namespace App\Livewire\Coordinator;

use Livewire\Component;
use App\Models\Agency;
use App\Models\Location;
use App\Models\AgencyRequest;
use Illuminate\Support\Facades\Auth;

class CompanyManagement extends Component
{
    public $agencies;
    public $requestedAgencies;
    public $open = false;
    public $newCompanyName;
    public $newCompanyBackground; // Changed from newCompanyIndustry
    public $newCompanyDescription;
    public $latitude;
    public $longitude;
    public $locationName; // New property for location name

    protected $rules = [
        'newCompanyName' => 'required|string|max:255',
        'newCompanyBackground' => 'nullable|string', // Updated rule
        'newCompanyDescription' => 'nullable|string',
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
        'locationName' => 'nullable|string|max:255', // Rule for location name
    ];

    public function mount()
    {
        $this->agencies = Agency::with(['contactPerson', 'location'])->get();
        $this->requestedAgencies = AgencyRequest::with('location')
            ->where('requested_by_user_id', Auth::id())
            ->get();
    }

    public function requestCompany()
    {
        $this->validate();

        $location = Location::create([
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'location_name' => $this->locationName, // Save the location name
        ]);

        AgencyRequest::create([
            'agency_name' => $this->newCompanyName,
            'agency_background' => $this->newCompanyBackground, // Updated attribute
            'description' => $this->newCompanyDescription,
            'location_id' => $location->id,
            'requested_by_user_id' => Auth::id(),
        ]);

        session()->flash('message', 'Company requested successfully.');
        $this->reset();
        $this->mount();
    }

    public function render()
    {
        return view('livewire.coordinator.company-management')
            ->layout('layouts.auth-layout');
    }
}