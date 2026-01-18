<?php

namespace App\Livewire\Intern;

use Livewire\Component;
use App\Models\User;
use App\Models\Agency;
use App\Models\Location;
use App\Models\Schedule;
use App\Models\DailyLog;
use App\Models\Course;
use Illuminate\Support\Facades\Storage;

class AttendancePortal extends Component
{
    public $user;
    public $isInside = false;
    public $locationName;
    public $longitude;
    public $latitude;
    public $agencyLatitude;
    public $agencyLongitude;
    public $schedule;
    public $currentTime;
    public $morningInTime;
    public $morningOutTime;
    public $afternoonInTime;
    public $afternoonOutTime;
    public $todayLog;
    public $activeSessions = 0;
    public $selectedDate;
    public $pastDates = [];
    public $futureDates = [];
    public $allDates = [];
    public bool $isCheckInEnabled = false;
    public $todaySignature = null;
    public $signatureNeeded = true;
    public $buttonStatus = 'too_early_morning'; 


    public function mount()
{
    $this->user = auth()->user();
    $this->fetchLocation(); // Get user's location
    $this->fetchAgencyLocation(); // Get agency's location
    $this->checkLocation();
    $this->loadSchedule(); // Load the user's schedule
    $this->checkSchedule(); // Initial check of the schedule
    $this->loadTodayLog(); // Load today's log
    $this->loadTodayLogTimes(); // Load today's log times for display
    $this->updateSignatureNeeded(); // Add this line to update the signature status
    $this->currentTime = now(config('app.timezone'));
    $this->selectedDate = now(config('app.timezone'))->toDateString();
    $this->generateDateSlider();
    $this->updateTodaySignature();
    $this->checkForAutoCheckout(); // Check for auto-checkout
    
    if ($this->todayLog && (!$this->todayLog->morning_out || !$this->todayLog->afternoon_out)) {
        $this->dispatch('startCountdown');
    }
}

    
    public function checkForAutoCheckout()
{
    if (!$this->schedule) {
        return;
    }
    
    $now = now(config('app.timezone'));
    $gracePeriod = $this->schedule->grace_period ?? 5; // Default to 5 minutes if not set
    
    // Morning session auto-checkout (always uses grace period)
    if ($this->todayLog && $this->todayLog->morning_in && !$this->todayLog->morning_out) {
        if ($this->schedule->expected_morning_out) {
            $expectedMorningOut = \Carbon\Carbon::parse($this->schedule->expected_morning_out);
            $autoCheckoutTime = $expectedMorningOut->copy()->addMinutes($gracePeriod);
            
            if ($now->gt($autoCheckoutTime)) {
                $this->todayLog->update([
                    'morning_out' => $expectedMorningOut->toTimeString(),
                    'status' => 'late'
                ]);
                $this->calculateHoursRendered($this->todayLog);
                $this->loadTodayLog();
                $this->loadTodayLogTimes();
                $this->dispatch('logUpdated');
                
                if (!$this->todayLog->afternoon_in) {
                    $this->dispatch('stopCountdown');
                }
            }
        }
    }
    
    // Afternoon session auto-checkout (uses overtime_allowed if available)
    if ($this->todayLog && $this->todayLog->afternoon_in && !$this->todayLog->afternoon_out) {
        if ($this->schedule->expected_afternoon_out) {
            $expectedAfternoonOut = \Carbon\Carbon::parse($this->schedule->expected_afternoon_out);
            
            // Use overtime_allowed if available, otherwise use grace period
            if ($this->schedule->overtime_allowed) {
                $overtimeEnd = \Carbon\Carbon::parse($this->schedule->overtime_allowed);
                $autoCheckoutTime = $overtimeEnd;
            } else {
                $autoCheckoutTime = $expectedAfternoonOut->copy()->addMinutes($gracePeriod);
            }
            
            if ($now->gt($autoCheckoutTime)) {
                $this->todayLog->update([
                    'afternoon_out' => $autoCheckoutTime->toTimeString(),
                    'status' => 'late'
                ]);
                $this->calculateHoursRendered($this->todayLog);
                $this->loadTodayLog();
                $this->loadTodayLogTimes();
                $this->dispatch('logUpdated');
                $this->dispatch('stopCountdown');
            }
        }
    }
}

    public function calculateHoursRendered($dailyLog)
    {
        if (!$dailyLog) return 0;
        
        $totalHours = 0;
        
        // Calculate morning hours (ensure non-negative)
        if ($dailyLog->morning_in && $dailyLog->morning_out) {
            $morningIn = \Carbon\Carbon::parse($dailyLog->morning_in);
            $morningOut = \Carbon\Carbon::parse($dailyLog->morning_out);
            $morningDuration = max(0, $morningOut->diffInSeconds($morningIn)); // Ensure >= 0
            $totalHours += $morningDuration / 3600;
        }
        
        // Calculate afternoon hours (ensure non-negative)
        if ($dailyLog->afternoon_in && $dailyLog->afternoon_out) {
            $afternoonIn = \Carbon\Carbon::parse($dailyLog->afternoon_in);
            $afternoonOut = \Carbon\Carbon::parse($dailyLog->afternoon_out);
            $afternoonDuration = max(0, $afternoonOut->diffInSeconds($afternoonIn)); // Ensure >= 0
            $totalHours += $afternoonDuration / 3600;
        }
        
        $totalHours = round($totalHours, 2);
        $dailyLog->update(['hours_rendered' => $totalHours]);
        
        return $totalHours;
    }

    
    
   
    
    public function updateRemainingHours($remainingHours)
    {
        if (auth()->check()) {
            auth()->user()->update(['remaining_hours' => round($remainingHours, 2)]); // Or however you want to round
            $this->user->refresh(); // Refresh the user model to reflect the change
        }
    }

    public function fetchLocation()
    {
        $this->locationName = $this->user->location->location_name ?? 'Unknown Location';
        $this->longitude = $this->user->location->longitude ?? '0';
        $this->latitude = $this->user->location->latitude ?? '0';
    }

    public function fetchAgencyLocation()
    {
        $agency = $this->user->agency;
        $this->agencyLatitude = $agency->location->latitude ?? '51.5074'; // Default to London
        $this->agencyLongitude = $agency->location->longitude ?? '-0.1278'; // Default to London
    }

    public function checkLocation()
    {
        if (!$this->user || !$this->user->location) return;

        $userLat = $this->user->location->latitude;
        $userLng = $this->user->location->longitude;
        $agency = $this->user->agency;

        if (!$userLat || !$userLng || !$agency || !$agency->location) return;

        // Convert meters to miles (malhal/geographical uses miles)
        $radiusInMiles = $agency->agency_radius * 0.000621371;

        // Check if user is within the agency's allowed zone
        $distance = $this->calculateDistance($userLat, $userLng, $agency->location->latitude, $agency->location->longitude);

        if ($distance <= $radiusInMiles) {
            $this->isInside = true;
                  
        } else {
            $this->isInside = false;
        }
         \Log::info(' inside: ' . var_export($this->isInside, true));
    }
    
    
    
   public function loadSchedule()
{
    // Get the schedules for the intern's agency
    $agencySchedules = Schedule::where('agency_id', $this->user->agency_id)->get();
  
    // Find the schedule for the current day of the week
    $this->schedule = $agencySchedules->where('day_of_week', now()->format('l'))->first();
    
    // Initialize empty schedule if none found
    if (!$this->schedule) {
        $this->schedule = new Schedule([
            'day_of_week' => now()->format('l'),
            'expected_morning_in' => null,
            'expected_morning_out' => null,
            'expected_afternoon_in' => null,
            'expected_afternoon_out' => null,
            'late_tolerance' => 30,
        ]);
    }
}




    public function hasCheckedIn($period = 'morning')
    {
        if ($this->todayLog) {
            return $period === 'morning' ? $this->todayLog->morning_in : $this->todayLog->afternoon_in;
        }
        return false;
    }
    
    public function loadTodayLog()
{
    $this->todayLog = DailyLog::where('user_id', $this->user->id)
        ->where('date', now()->toDateString())
        ->first();
    
    // Remove the toArray() conversion
    // if ($this->todayLog) {
    //     $this->todayLog = $this->todayLog->toArray();
    // }
    
    $this->activeSessions = 0;
    if ($this->todayLog && $this->todayLog->morning_in) {
        $this->activeSessions = 1;
        if ($this->todayLog->afternoon_in) {
            $this->activeSessions = 2;
        }
    }
}
    
    public function generateDateSlider()
    {
        $today = now(config('app.timezone'))->startOfDay();
    
        // Load past logs for user (limit to last 7)
        $pastLogs = DailyLog::where('user_id', $this->user->id)
            ->where('date', '<', $today->toDateString())
            ->orderBy('date', 'desc')
            ->take(7)
            ->get()
            ->pluck('date')
            ->map(fn ($date) => \Carbon\Carbon::parse($date)->toDateString())
            ->reverse()
            ->toArray();
    
        // Future dates (up to 7 from tomorrow)
        $futureDates = collect(range(1, 7))
            ->map(fn ($i) => $today->copy()->addDays($i)->toDateString())
            ->toArray();
    
        $this->pastDates = $pastLogs;
        $this->futureDates = $futureDates;
    
        $this->allDates = array_merge($this->pastDates, [$today->toDateString()], $this->futureDates);
    }
    
    public function selectDate($date)
    {
        $this->selectedDate = $date;
    
        // If it's today, load normally
        if ($date === now(config('app.timezone'))->toDateString()) {
            $this->loadTodayLog();
        } else {
            // Load different date log
            $this->todayLog = DailyLog::where('user_id', $this->user->id)
                ->where('date', $date)
                ->first();
        }
    
        $this->loadTodayLogTimes();
    }



    public function checkSchedule()
{
    if (!$this->schedule) {
        $this->isCheckInEnabled = false;
        $this->buttonStatus = 'no_schedule';
        return;
    }
    
    $now = now(config('app.timezone'));
    $lateTolerance = $this->schedule->late_tolerance ?? 30;
    $gracePeriod = $this->schedule->grace_period ?? 5;
    
    $this->isCheckInEnabled = false;
    
    // Morning session logic
    $expectedMorningIn = \Carbon\Carbon::parse($this->schedule->expected_morning_in);
    $expectedMorningOut = \Carbon\Carbon::parse($this->schedule->expected_morning_out);
    $morningInAllowedUntil = $expectedMorningIn->copy()->addMinutes($lateTolerance);
    $morningOutAllowedUntil = $expectedMorningOut->copy()->addMinutes($gracePeriod);
    
    // Afternoon session logic
    $expectedAfternoonIn = $this->schedule->expected_afternoon_in 
        ? \Carbon\Carbon::parse($this->schedule->expected_afternoon_in)
        : null;
    $expectedAfternoonOut = $this->schedule->expected_afternoon_out
        ? \Carbon\Carbon::parse($this->schedule->expected_afternoon_out)
        : null;
        
    // Determine afternoon checkout end time
    $afternoonOutAllowedUntil = $expectedAfternoonOut
        ? ($this->schedule->overtime_allowed 
            ? \Carbon\Carbon::parse($this->schedule->overtime_allowed)
            : $expectedAfternoonOut->copy()->addMinutes($gracePeriod))
        : null;
        
    $afternoonInAllowedUntil = $expectedAfternoonIn 
        ? $expectedAfternoonIn->copy()->addMinutes($lateTolerance)
        : null;
    
    // Check if we're in morning check-in window
    if ($now->lt($expectedMorningIn)) {
        $this->buttonStatus = 'too_early_morning';
    } elseif ($now->gte($expectedMorningIn) && $now->lte($morningInAllowedUntil) && !$this->todayLog?->morning_in) {
        $this->isCheckInEnabled = true;
        $this->buttonStatus = 'morning_check_in';
    } elseif ($this->todayLog?->morning_in && !$this->todayLog?->morning_out) {
        if ($now->lt($expectedMorningOut)) {
            $this->buttonStatus = 'wait_morning_check_out';
        } elseif ($now->lte($morningOutAllowedUntil)) {
            $this->buttonStatus = 'morning_check_out';
        } else {
            $this->buttonStatus = 'morning_check_out_expired';
        }
    } elseif ($expectedAfternoonIn && $now->lt($expectedAfternoonIn)) {
        $this->buttonStatus = 'between_sessions';
    } elseif ($expectedAfternoonIn && $now->gte($expectedAfternoonIn) && $now->lte($afternoonInAllowedUntil) && !$this->todayLog?->afternoon_in) {
        $this->isCheckInEnabled = true;
        $this->buttonStatus = 'afternoon_check_in';
    } elseif ($this->todayLog?->afternoon_in && !$this->todayLog?->afternoon_out) {
        if ($now->lt($expectedAfternoonOut)) {
            $this->buttonStatus = 'wait_afternoon_check_out';
        } elseif ($now->lte($afternoonOutAllowedUntil)) {
            $this->buttonStatus = 'afternoon_check_out';
        } else {
            $this->buttonStatus = 'afternoon_check_out_expired';
        }
    } else {
        $this->buttonStatus = 'no_action_available';
    }
    
    $this->checkForAutoCheckout();
}
        
    

    // Haversine formula to calculate the distance
    public function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Distance in kilometers
    }
    
    
    
    // Modify the submitCheckIn method in AttendancePortal.php

public function submitCheckIn($signatureData)
{
    \Log::info('--- submitCheckIn called ---');
    \Log::info('isCheckInEnabled: ' . var_export($this->isCheckInEnabled, true));
    \Log::info('signatureData length: ' . strlen($signatureData));

    if (!$this->isCheckInEnabled) {
        session()->flash('error', 'Check-in is not allowed at this time.');
        \Log::info('Check-in blocked: isCheckInEnabled=' . var_export($this->isCheckInEnabled, true));
        return;
    }

    $now = now(config('app.timezone'));
    $logDate = $now->toDateString();

    \Log::info('Current Time (PST): ' . $now);
    \Log::info('Log Date (PST): ' . $logDate);

    $existingLog = DailyLog::where('user_id', $this->user->id)
        ->where('date', $logDate)
        ->first();

    \Log::info('Existing Log: ' . var_export($existingLog, true));
    
    // Check if we need a signature for this check-in
    $needsSignature = !$existingLog || !$existingLog->signature;
    
    if ($needsSignature && empty($signatureData)) {
        session()->flash('error', 'Signature is required for your first check-in of the day.');
        return;
    }

    $logData = [
        'user_id' => $this->user->id,
        'date' => $logDate,
        'status' => 'pending', // Initial status
    ];

    \Log::info('Initial logData: ' . json_encode($logData));

    // Save the signature to storage only if needed
    if ($needsSignature && !empty($signatureData)) {
        try {
            $image = str_replace('data:image/png;base64,', '', $signatureData);
            $image = str_replace(' ', '+', $image);
            $imageName = 'signature_' . time() . '.png';
    
            Storage::disk('public')->put("signatures/checkin/{$imageName}", base64_decode($image));
            $logData['signature'] = "signatures/checkin/{$imageName}";
            \Log::info('Signature saved: ' . $logData['signature']);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to save signature.');
            \Log::error('Error saving signature: ' . $e->getMessage());
            return;
        }
    }

    if ($this->schedule) {
        \Log::info('Schedule found: ' . var_export($this->schedule, true));
        $checkInTime = $now->toTimeString();
        $expectedMorningInPST = \Carbon\Carbon::parse($this->schedule->expected_morning_in);
        $morningInAllowedUntilPST = $expectedMorningInPST->copy()->addMinutes($this->schedule->late_tolerance);
        $expectedAfternoonInPST = \Carbon\Carbon::parse($this->schedule->expected_afternoon_in);
        $afternoonInAllowedUntilPST = $expectedAfternoonInPST->copy()->addMinutes($this->schedule->late_tolerance);

        \Log::info('Check-in Time (PST): ' . $checkInTime);
        \Log::info('Expected Morning In (PST): ' . $expectedMorningInPST);
        \Log::info('Morning In Allowed Until (PST): ' . $morningInAllowedUntilPST);
        \Log::info('Expected Afternoon In (PST): ' . $expectedAfternoonInPST);
        \Log::info('Afternoon In Allowed Until (PST): ' . $afternoonInAllowedUntilPST);

        if (!$existingLog) {
            \Log::info('No existing log for today.');
            // Check for morning check-in
            if ($now->gte($expectedMorningInPST) && $now->lte($morningInAllowedUntilPST)) {
                $logData['morning_in'] = $checkInTime;
                if ($now->gt($expectedMorningInPST)) {
                    $logData['status'] = 'late';
                }
                \Log::info('Creating new DailyLog (morning) with data: ' . json_encode($logData));
                $this->todayLog = DailyLog::create($logData);
                \Log::info('New DailyLog created: ' . var_export($this->todayLog, true));
                $this->activeSessions = 1;
                session()->flash('success', 'Checked in successfully!');
            }
            // Check for afternoon check-in
            elseif ($this->schedule->expected_afternoon_in) {
                if ($now->gte($expectedAfternoonInPST) && $now->lte($afternoonInAllowedUntilPST)) {
                    $logData['afternoon_in'] = $checkInTime;
                    \Log::info('Creating new DailyLog (afternoon) with data: ' . json_encode($logData));
                    $this->todayLog = DailyLog::create($logData);
                    \Log::info('New DailyLog created (afternoon): ' . var_export($this->todayLog, true));
                    $this->activeSessions = 1; // Assuming first check-in is the afternoon one
                    session()->flash('success', 'Checked in successfully for the afternoon!');
                } else {
                    session()->flash('error', 'Check-in is not allowed at this time.');
                    \Log::info('Check-in not allowed (new log - afternoon): Current time not within afternoon window.');
                }
            } else {
                session()->flash('error', 'Check-in is not allowed at this time.');
                \Log::info('Check-in not allowed (new log): Not within any allowed window.');
            }
        } elseif (!$existingLog->morning_in) {
            \Log::info('Existing log found, morning_in is null.');
            if ($now->gte($expectedMorningInPST) && $now->lte($morningInAllowedUntilPST)) {
                \Log::info('Updating existing DailyLog with morning_in: ' . $checkInTime . ' and logData: ' . json_encode($logData));
                $existingLog->update(['morning_in' => $checkInTime] + $logData);
                $this->todayLog = $existingLog;
                \Log::info('DailyLog updated: ' . var_export($this->todayLog->toArray(), true));
                $this->activeSessions = 1;
                session()->flash('success', 'Checked in successfully!');
            } else {
                session()->flash('error', 'Check-in is not allowed at this time.');
                \Log::info('Check-in not allowed (existing log - morning): Current time not within morning window.');
            }
        } elseif (!$existingLog->afternoon_in && $this->schedule->expected_afternoon_in && $now->gte($expectedAfternoonInPST->subMinutes($this->schedule->late_tolerance))) {
            \Log::info('Existing log found, afternoon_in is null and afternoon window is active.');
            $existingLog->update(['afternoon_in' => $checkInTime] + $logData);
            $this->todayLog = $existingLog;
            $this->activeSessions = 2;
            session()->flash('success', 'Checked in for the afternoon!');
            \Log::info('DailyLog updated for afternoon: ' . var_export($this->todayLog->toArray(), true));
        } else {
            session()->flash('error', 'You have already checked in for all scheduled sessions.');
            \Log::info('Check-in blocked: All sessions already checked in.');
        }

        $this->loadTodayLogTimes();
    } else {
        session()->flash('error', 'No schedule found for today.');
        \Log::info('No schedule found for today.');
    }
    
    $this->updateTodaySignature();
    $this->updateSignatureNeeded();
    
    $this->loadTodayLog(); // Force reload the log
    $this->dispatch('logUpdated')->self(); // Dispatch to self
    $this->dispatch('checkInSuccessful');
    $this->dispatch('startCountdown');
    
    // Add this debug line:
    \Log::info('TodayLog after update:', $this->todayLog ? $this->todayLog->toArray() : 'null');
}

public function updateSignatureNeeded()
{
    $logDate = now(config('app.timezone'))->toDateString();
    $existingLog = DailyLog::where('user_id', $this->user->id)
        ->where('date', $logDate)
        ->first();
    
    $this->signatureNeeded = !$existingLog || !$existingLog->signature;
    
    \Log::info('Signature needed status: ' . ($this->signatureNeeded ? 'Yes' : 'No'));
    \Log::info('Existing log: ' . ($existingLog ? 'Yes' : 'No'));
}


    public function updateTodaySignature()
    {
        $logDate = now(config('app.timezone'))->toDateString();
        $this->todaySignature = DailyLog::where('user_id', $this->user->id)
            ->where('date', $logDate)
            ->value('signature');
        
        \Log::info('Today\'s signature status: ' . ($this->todaySignature ? 'Has signature' : 'No signature'));
    }
    
    public function checkCheckoutStatus()
    {
        if ($this->todayLog) {
            \Log::info('Current todayLog status:');
            \Log::info('morning_in: ' . ($this->todayLog->morning_in ?? 'null'));
            \Log::info('morning_out: ' . ($this->todayLog->morning_out ?? 'null'));
            \Log::info('afternoon_in: ' . ($this->todayLog->afternoon_in ?? 'null'));
            \Log::info('afternoon_out: ' . ($this->todayLog->afternoon_out ?? 'null'));
            
            return [
                'can_checkout_morning' => !empty($this->todayLog->morning_in) && empty($this->todayLog->morning_out),
                'can_checkout_afternoon' => !empty($this->todayLog->afternoon_in) && empty($this->todayLog->afternoon_out),
            ];
        }
        
        return [
            'can_checkout_morning' => false,
            'can_checkout_afternoon' => false,
        ];
    }



public function submitMorningCheckOut()
{
    \Log::info('submitMorningCheckOut called');
    $this->submitCheckOut('morning');
    $this->loadTodayLog(); // Reload to ensure data is fresh
    $this->loadTodayLogTimes(); // Update times for display
    $this->dispatch('checkoutSuccessful');
}

public function submitAfternoonCheckOut()
{
    \Log::info('submitAfternoonCheckOut called');
    $this->submitCheckOut('afternoon');
    $this->loadTodayLog(); // Reload to ensure data is fresh
    $this->loadTodayLogTimes(); // Update times for display
    $this->dispatch('checkoutSuccessful');
}

public function getListeners()
{
    return [
        'checkoutSuccessful' => '$refresh',
    ];
}

protected $listeners = [
    'logUpdated' => 'checkSchedule',
    'checkInSuccessful' => 'checkSchedule',
    'checkoutSuccessful' => 'checkSchedule'
];

public function submitCheckOut($period = 'morning')
{
    if (!$this->todayLog) {
        session()->flash('error', 'No check-in found for today.');
        return;
    }

    $now = now(config('app.timezone'));

    if ($period === 'morning') {
        if ($this->todayLog->morning_in && !$this->todayLog->morning_out) {
            // If you have schedule validation, you can keep it
            if ($this->schedule && $this->schedule->expected_morning_out) {
                $expectedMorningOut = \Carbon\Carbon::parse($this->schedule->expected_morning_out);
                // Check if it's within the allowed morning checkout window
                if ($now->lt($expectedMorningOut)) {
                    session()->flash('error', 'Early checkout not allowed for the morning session.');
                    return;
                }
            }
            
            $this->todayLog->update(['morning_out' => $now->toTimeString()]);
            $this->calculateHoursRendered($this->todayLog); 
            session()->flash('success', 'Checked out for the morning!');
            $this->loadTodayLog();
            $this->loadTodayLogTimes();
            
            // Stop countdown if afternoon hasn't started
            if (!$this->todayLog->afternoon_in) {
                $this->dispatch('stopCountdown');
            }
        } else {
            session()->flash('error', 'Cannot perform morning checkout.');
        }
    } elseif ($period === 'afternoon') {
        if ($this->todayLog->afternoon_in && !$this->todayLog->afternoon_out) {
            // If you have schedule validation, you can keep it
            if ($this->schedule && $this->schedule->expected_afternoon_out) {
                $expectedAfternoonOut = \Carbon\Carbon::parse($this->schedule->expected_afternoon_out);
                // Check if it's within the allowed afternoon checkout window
                if ($now->lt($expectedAfternoonOut)) {
                    session()->flash('error', 'Early checkout not allowed for the afternoon session.');
                    return;
                }
            }
            
            $this->todayLog->update(['afternoon_out' => $now->toTimeString()]);
            $this->calculateHoursRendered($this->todayLog); 
            session()->flash('success', 'Checked out for the afternoon!');
            $this->loadTodayLog();
            $this->loadTodayLogTimes();
            
            // Stop countdown for the day is complete
            $this->dispatch('stopCountdown');
        } else {
            session()->flash('error', 'Cannot perform afternoon checkout.');
        }
    }
    $this->dispatch('logUpdated');
}
    
    
    public function loadTodayLogTimes()
    {
        \Log::info('loadTodayLogTimes() called');
        \Log::info('Today Log in loadTodayLogTimes(): ' . json_encode($this->todayLog));
        if ($this->todayLog) {
            $this->morningInTime = $this->todayLog->morning_in ? \Carbon\Carbon::parse($this->todayLog->morning_in)->format('h:i A') : null;
            $this->morningOutTime = $this->todayLog->morning_out ? \Carbon\Carbon::parse($this->todayLog->morning_out)->format('h:i A') : null;
            $this->afternoonInTime = $this->todayLog->afternoon_in ? \Carbon\Carbon::parse($this->todayLog->afternoon_in)->format('h:i A') : null;
            $this->afternoonOutTime = $this->todayLog->afternoon_out ? \Carbon\Carbon::parse($this->todayLog->afternoon_out)->format('h:i A') : null;
        } else {
            $this->morningInTime = null;
            $this->morningOutTime = null;
            $this->afternoonInTime = null;
            $this->afternoonOutTime = null;
        }
    }



    public function render()
    {
        return view('livewire.intern.attendance-portal', [
            'serverTime' => now(config('app.timezone'))->toDateTimeString(),
            'morningOutTime' => $this->schedule->expected_morning_out ?? null,
            'afternoonOutTime' => $this->schedule->expected_afternoon_out ?? null,
        ])->layout('layouts.auth-layout');
    }
}
