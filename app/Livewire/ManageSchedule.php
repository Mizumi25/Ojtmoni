<?php

namespace App\Livewire;

use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Agency;

class ManageSchedule extends Component
{
    public $daysOfWeek = [
        'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday',
    ];

    public $schedules = [];
    public $selectedDay = null;
    public $expected_morning_in;
    public $expected_morning_out;
    public $expected_afternoon_in;
    public $expected_afternoon_out;
    public $late_tolerance;
    public $grace_period;
    public $overtime_allowed;
    public $showDeleteModal = false;
    public $deleteDay = null;
    public $contactPersonName;
    public $agencyName;
    public $agencyId;
    public $agencyList = []; // For admin/coordinator list
    public $showSlider = false; // Show or hide the "slider modal"



    public function mount($agencyId = null)
    {
        if (auth()->user()->hasRole(['admin', 'coordinator'])) {
            $this->agencyList = Agency::all(); // Admins/Coordinators get all agencies
            $this->showSlider = false;
        } else {
            $agency = Agency::where('contact_person_id', auth()->id())->first();
            if ($agency) {
                $this->agencyId = $agency->id;
                $this->agencyName = $agency->agency_name;
                $this->contactPersonName = $agency->contactPerson->name ?? 'No Name';
                $this->loadSchedules();
                $this->showSlider = true; // Directly show
            }
        }
        
        
    }



    public function loadSchedules()
    {
        if ($this->agencyId) {
            $this->schedules = Schedule::where('agency_id', $this->agencyId)
                ->get()
                ->keyBy('day_of_week')
                ->toArray();
        } else {
            $this->schedules = [];
        }
    }



    public function selectDay($day)
    {
        $this->selectedDay = $day;

        if (isset($this->schedules[$day])) {
            $sched = $this->schedules[$day];
            $this->expected_morning_in = $sched['expected_morning_in'];
            $this->expected_morning_out = $sched['expected_morning_out'];
            $this->expected_afternoon_in = $sched['expected_afternoon_in'];
            $this->expected_afternoon_out = $sched['expected_afternoon_out'];
            $this->late_tolerance = $sched['late_tolerance'];
            $this->grace_period = $sched['grace_period'];
            $this->overtime_allowed = $sched['overtime_allowed'];
        } else {
            $this->resetForm();
        }
    }
    
    public function openAgency($agencyId)
    {
        $agency = Agency::findOrFail($agencyId);
        $this->agencyId = $agency->id;
        $this->agencyName = $agency->agency_name;
        $this->contactPersonName = $agency->contactPerson->name ?? 'No Name';
        $this->loadSchedules();
        $this->showSlider = true;
    }


    public function resetForm()
    {
        $this->expected_morning_in = null;
        $this->expected_morning_out = null;
        $this->expected_afternoon_in = null;
        $this->expected_afternoon_out = null;
        $this->grace_period = null;
        $this->late_tolerance = null;
        $this->overtime_allowed = null;
    }

    public function save()
    {
        $agencyId = $this->agencyId;

        if (!$agencyId) {
            session()->flash('error', 'No agency selected.');
            return;
        }

        $validated = $this->validate([
            'expected_morning_in' => 'required',
            'expected_morning_out' => 'required',
            'expected_afternoon_in' => 'required',
            'expected_afternoon_out' => 'required',
            'late_tolerance' => 'nullable|integer',
            'grace_period' => 'nullable|integer',
            'overtime_allowed' => 'nullable',
        ]);
        
      $validated['late_tolerance'] = $validated['late_tolerance'] ?? 0;
      $validated['grace_period'] = $validated['grace_period'] ?? 0;


        Schedule::updateOrCreate(
            [
                'agency_id' => $agencyId,
                'day_of_week' => $this->selectedDay,
            ],
            array_merge($validated, ['user_id' => Auth::id()])
        );

        $this->loadSchedules();
        $this->resetForm();
        $this->selectedDay = null;
        session()->flash('message', 'Schedule saved successfully.');
    }

    public function confirmDelete($day)
    {
        $this->deleteDay = $day;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $agencyId = $this->agencyId;

        if (!$agencyId) {
            session()->flash('error', 'No agency selected.');
            return;
        }


        Schedule::where('agency_id', $agencyId)
            ->where('day_of_week', $this->deleteDay)
            ->delete();

        $this->loadSchedules();
        $this->showDeleteModal = false;
        $this->deleteDay = null;
        session()->flash('message', 'Schedule deleted successfully.');
    }

    public function render()
    {
        return view('livewire.manage-schedule')
            ->layout('layouts.auth-layout');
    }
}
