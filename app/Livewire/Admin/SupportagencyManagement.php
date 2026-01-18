<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Agency;
use App\Models\Location;
use App\Models\AgencyRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage; 

class SupportagencyManagement extends Component
{
  use WithFileUploads;
  
  
    public $agencies;
    public $agencyRequests;
    public $open = false;
    public $image; 
    public $activeTab = 'agencies';
    public $isViewingRequest = false;
    public $selectedAgency = [
        'id' => null,
        'agency_name' => '',
        'agency_background' => '',
        'agency_number' => '',
        'slot' => '',
        'agency_radius' => '',
        'location' => [
            'location_name' => '',
            'latitude' => '',
            'longitude' => '',
        ],
        'agency_image' => null, 
    ];
    public $selectedRequest = [
        'id' => null,
        'agency_name' => '',
        'agency_background' => '',
        'location' => [
            'location_name' => '',
            'latitude' => null,
            'longitude' => null,
        ],
        'requested_by_user_id' => null,
        'requester_name' => '',
        'requester_course' => '',
    ];

    protected $listeners = ['agencySelected', 'agencyRequestSelected'];

    protected $rules = [
        'selectedAgency.agency_name' => 'required|string|max:255',
        'selectedAgency.agency_background' => 'nullable|string',
        'selectedAgency.agency_number' => 'nullable|string|max:20',
        'selectedAgency.slot' => 'required|integer|min:0',
        'selectedAgency.agency_radius' => 'nullable|numeric|min:0',
        'selectedAgency.location.location_name' => 'required|string|max:255',
        'selectedAgency.location.latitude' => 'nullable|numeric',
        'selectedAgency.location.longitude' => 'nullable|numeric',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ];

    public function mount()
    {
        $this->agencies = Agency::with(['contactPerson', 'location'])->get();
        $this->agencyRequests = AgencyRequest::with('requester', 'location')->get();
    }

    public function selectAgency($id)
    {
        $agency = Agency::with('location')->find($id);
        if ($agency) {
            $this->selectedAgency = [
                'id' => $agency->id,
                'agency_name' => $agency->agency_name,
                'agency_background' => $agency->agency_background,
                'agency_number' => $agency->agency_number,
                'slot' => $agency->slot,
                'agency_radius' => $agency->agency_radius,
                'location' => [
                    'location_name' => $agency->location->location_name ?? '',
                    'latitude' => $agency->location->latitude ?? '',
                    'longitude' => $agency->location->longitude ?? '',
                ],
                'agency_image' => $agency->agency_image,
            ];
            $this->dispatch('agencySelected', $this->selectedAgency);
            $this->open = true;
            $this->isViewingRequest = false;
            $this->image = null;
        }
    }

    public function selectAgencyRequest($id)
    {
        $request = AgencyRequest::with('requester', 'location')->find($id);
        if ($request) {
            $this->selectedRequest = [
                'id' => $request->id,
                'agency_name' => $request->agency_name,
                'agency_background' => $request->agency_background,
                'location' => [
                    'location_name' => $request->location->location_name ?? '',
                    'latitude' => $request->location->latitude ?? null,
                    'longitude' => $request->location->longitude ?? null,
                ],
                'requested_by_user_id' => $request->requested_by_user_id,
                'requester_name' => $request->requester->name ?? 'N/A',
                'requester_course' => $request->requester->course->abbreviation ?? 'N/A',
            ];
            $this->dispatch('agencyRequestSelected', $this->selectedRequest);
            $this->open = true;
            $this->isViewingRequest = true;

            $this->selectedAgency = [
                'id' => null,
                'agency_name' => '',
                'agency_background' => '',
                'agency_number' => '',
                'slot' => '',
                'agency_radius' => '',
                'location' => [
                    'location_name' => '',
                    'latitude' => '',
                    'longitude' => '',
                ],
            ];
        }
    }

    public function approveAgencyRequest($id)
    {
        $agencyRequest = AgencyRequest::with('location', 'requester')->find($id);

        if ($agencyRequest) {
            // Create a new user for the agency contact person
            $generatedPassword = Str::slug($agencyRequest->agency_name) . '123';
            $user = User::create([
                'name' => $agencyRequest->agency_name . ' Contact',
                'email' => Str::slug($agencyRequest->agency_name) . '@gcc.com',
                'password' => Hash::make($generatedPassword),
                'role' => 'agency',
            ]);

            // Create the location for the new agency
            $location = Location::create([
                'location_name' => $agencyRequest->location->location_name,
                'latitude' => $agencyRequest->location->latitude,
                'longitude' => $agencyRequest->location->longitude,
            ]);

            // Create the new agency
            $agency = Agency::create([
                'agency_name' => $agencyRequest->agency_name,
                'agency_background' => $agencyRequest->agency_background,
                'contact_person_id' => $user->id,
                'location_id' => $location->id,
                // You might want to add default values for agency_number, slot, and agency_radius
                'agency_number' => '',
                'slot' => 0,
                'agency_radius' => null,
            ]);

            // Delete the agency request
            $agencyRequest->location()->delete(); // Delete associated location
            $agencyRequest->delete();

            session()->flash('message', 'Agency request approved. Agency "' . $agency->agency_name . '" created. Default password for contact person is: ' . $generatedPassword);
            $this->agencies = Agency::with(['contactPerson', 'location'])->get();
            $this->agencyRequests = AgencyRequest::with('requester', 'location')->get();
            $this->resetSelectedRequest();
            $this->open = false;
            $this->activeTab = 'agencies';
            $this->isViewingRequest = false;
        } else {
            session()->flash('error', 'Could not find agency request to approve.');
        }
    }

    public function disapproveAgencyRequest($id)
    {
        $agencyRequest = AgencyRequest::find($id);
        if ($agencyRequest) {
            $agencyRequest->location()->delete(); // Delete associated location
            $agencyRequest->delete();
            session()->flash('message', 'Agency request disapproved and deleted.');
            $this->agencyRequests = AgencyRequest::with('requester', 'location')->get();
            $this->resetSelectedRequest();
            $this->open = false;
            $this->isViewingRequest = false;
        } else {
            session()->flash('error', 'Could not find agency request to disapprove.');
        }
    }

    public function resetSelectedRequest()
    {
        $this->selectedRequest = [
            'id' => null,
            'agency_name' => '',
            'agency_background' => '',
            'location' => [
                'location_name' => '',
                'latitude' => null,
                'longitude' => null,
            ],
            'requested_by_user_id' => null,
            'requester_name' => '',
            'requester_course' => '',
        ];
    }

    public function saveAgency()
    {
        $this->validate();

        $agency = Agency::find($this->selectedAgency['id']);

        if ($agency) {
            $agency->update([
                'agency_name' => $this->selectedAgency['agency_name'],
                'agency_background' => $this->selectedAgency['agency_background'],
                'agency_number' => $this->selectedAgency['agency_number'],
                'slot' => $this->selectedAgency['slot'],
                'agency_radius' => $this->selectedAgency['agency_radius'],
            ]);

            if ($agency->location) {
                $agency->location->update($this->selectedAgency['location']);
            } else {
                $location = new Location($this->selectedAgency['location']);
                $agency->location()->save($location);
            }
            
            if ($this->image) {
                if ($agency->agency_image) {
                    Storage::delete($agency->agency_image); // Delete old image
                }
                $path = $this->image->store('agency_images', 'public');
                $agency->update(['agency_image' => $path]);
            }

            session()->flash('message', 'Agency updated successfully.');
            $this->agencies = Agency::with(['contactPerson', 'location'])->get();
        } else {
            session()->flash('error', 'Could not find agency to update.');
        }
    }


    public function createAgency()
    {
        $this->validate();

        $generatedPassword = $this->selectedAgency['agency_name'] . '123';

        $user = User::create([
            'name' => $this->selectedAgency['agency_name'] . ' Contact',
            'email' => Str::slug($this->selectedAgency['agency_name']) . '@gcc.com',
            'password' => Hash::make($generatedPassword),
            'role' => 'agency',
        ]);

        // Create the new agency and associate the contact person's ID
        $agency = Agency::create([
            'agency_name' => $this->selectedAgency['agency_name'],
            'agency_background' => $this->selectedAgency['agency_background'],
            'contact_person_id' => $user->id,
            'agency_number' => $this->selectedAgency['agency_number'],
            'slot' => $this->selectedAgency['slot'],
            'agency_radius' => $this->selectedAgency['agency_radius'],
        ]);
        
        if ($this->image) {
            $path = $this->image->store('agency_images', 'public');
            $agency->update(['agency_image' => $path]);
        }
        
        // Create the location for the agency
        $location = new Location($this->selectedAgency['location']);
        $location->save(); // Save the new location to the database

        // Associate the location with the agency
        $agency->location()->associate($location);
        $agency->save(); // Save the agency with the location_id
        
        $this->image = null;

        session()->flash('message', 'Agency created successfully. Default password for contact person is: ' . $generatedPassword);
        $this->agencies = Agency::with(['contactPerson', 'location'])->get();

        $this->resetSelectedAgency();
        $this->open = false;
    }
    
    public function deleteAgency($id)
    {
        $agency = Agency::find($id);
        if ($agency && $agency->agency_logo) {
            Storage::delete($agency->agency_logo); // Delete the agency logo if it exists
        }
        if ($agency) {
            $agency->location()->delete(); // Delete the associated location
            $agency->contactPerson()->delete(); // Optionally delete the associated contact person user
            $agency->delete();
            session()->flash('message', 'Agency deleted successfully.');
            $this->agencies = Agency::with(['contactPerson', 'location'])->get();
            $this->resetSelectedAgency();
            $this->open = false;
        } else {
            session()->flash('error', 'Could not find agency to delete.');
        }
    }

    public function resetSelectedAgency()
    {
        $this->selectedAgency = [
            'id' => null,
            'agency_name' => '',
            'agency_background' => '',
            'agency_number' => '',
            'slot' => '',
            'agency_radius' => '',
            'location' => [
                'location_name' => '',
                'latitude' => '',
                'longitude' => '',
            ],
            'agency_image' => null,
        ];
        $this->image = null;
    }

    public function goToTrackingMap($id)
    {
        return redirect()->route('tracks.agency', ['agency_id' => $id]);
    }

    public function render()
    {
        return view('livewire.admin.supportagency-management')
            ->layout('layouts.auth-layout');
    }
}