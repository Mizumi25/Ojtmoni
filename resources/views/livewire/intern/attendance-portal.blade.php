<!-- Replace the existing Alpine.js configuration with this updated version -->
<div
    class="relative w-full h-screen bg-transparent flex flex-col items-center justify-end"
    wire:init="checkLocation"
    <div
    class="relative w-full h-screen bg-transparent flex flex-col items-center justify-end"
    wire:init="checkLocation"
    x-data="{
    inside: @entangle('isInside'),
    showSignatureModal: false,
    signaturePad: null,
    canvasRef: null,
    isCheckInEnabled: @entangle('isCheckInEnabled'),
    todayLog: @js($todayLog ? $todayLog->toArray() : null),
    remainingHours: {{ auth()->user()->course->remaining_hours ?? 0 }},
    morningInTime: @entangle('morningInTime').defer,
    morningOutTime: @entangle('morningOutTime').defer,
    afternoonInTime: @entangle('afternoonInTime').defer,
    afternoonOutTime: @entangle('afternoonOutTime').defer,
    
    // Check if it's possible to check out
    get canCheckOut() {
        if (!this.todayLog) {
            return false;
        }
        
        // If there's a check-in without a checkout, enable checkout
        const morningCheck = this.todayLog.morning_in && !this.todayLog.morning_out;
        const afternoonCheck = this.todayLog.afternoon_in && !this.todayLog.afternoon_out;
        
        return morningCheck || afternoonCheck;
    },
        
    get isInMorningSession() {
        return this.todayLog && this.todayLog.morning_in && !this.todayLog.morning_out;
    },
    
    get isInAfternoonSession() {
        return this.todayLog && this.todayLog.afternoon_in && !this.todayLog.afternoon_out;
    },
        
    // Determine the text for checkout button
    get checkOutButtonLabel() {
        if (this.isInMorningSession) {
            return 'Morning Check Out';
        } else if (this.isInAfternoonSession) {
            return 'Afternoon Check Out';
        }
        return 'Check Out';
    },
    
    get buttonText() {
    if (!this.inside) return 'Outside Allowed Zone';
    
    switch ($wire.buttonStatus) {
        case 'too_early_morning':
            return $wire.schedule?.expected_morning_in 
                ? 'Too Early (Wait until ' + $wire.schedule.expected_morning_in + ')' 
                : 'Too Early (No schedule)';
        case 'morning_check_in':
            return 'Morning Check In';
        case 'wait_morning_check_out':
            return 'Wait till Check Out';
        case 'morning_check_out':
            return 'Morning Check Out';
        case 'morning_check_out_expired':
            return 'Morning Check Out Expired';
        case 'between_sessions':
            return 'Wait for Afternoon Session';
        case 'afternoon_check_in':
            return 'Afternoon Check In';
        case 'wait_afternoon_check_out':
            return 'Wait till Check Out';
        case 'afternoon_check_out':
            return 'Afternoon Check Out';
        case 'afternoon_check_out_expired':
            return 'Afternoon Check Out Expired';
        case 'no_schedule':
            return 'No Schedule Available';
        case 'no_action_available':
            return 'No Action Available';
        default:
            return 'Check In';
    }
},

get buttonClass() {
    if (!this.inside) {
        return 'bg-gray-300 text-gray-500 cursor-not-allowed';
    }
    
    switch ($wire.buttonStatus) {
        case 'morning_check_in':
        case 'afternoon_check_in':
            return 'bg-emerald-500 text-white hover:bg-emerald-600';
        case 'morning_check_out':
        case 'afternoon_check_out':
            return 'bg-red-500 text-white hover:bg-red-600';
        case 'morning_check_out_expired':
        case 'afternoon_check_out_expired':
            return 'bg-gray-300 text-gray-500 cursor-not-allowed';
        default:
            return 'bg-gray-300 text-gray-500 cursor-not-allowed';
    }
},

get isButtonDisabled() {
    if (!this.inside) return true;
    
    switch ($wire.buttonStatus) {
        case 'morning_check_in':
        case 'afternoon_check_in':
        case 'morning_check_out':
        case 'afternoon_check_out':
            return false;
        default:
            return true;
    }
},
    
    
    handleButtonClick() {
        if (!this.inside || this.isButtonDisabled) return;
        
        // Check if we're in check-in or check-out mode
        if ($wire.buttonStatus === 'morning_check_in' || $wire.buttonStatus === 'afternoon_check_in') {
            this.showSignatureModal = true;
            setTimeout(() => this.initSignaturePad(), 100);
        } else if ($wire.buttonStatus === 'morning_check_out') {
            $wire.submitMorningCheckOut();
        } else if ($wire.buttonStatus === 'afternoon_check_out') {
            $wire.submitAfternoonCheckOut();
        }
    },
    
    // Call the appropriate checkout method based on session
    callCheckOutMethod() {
        if (this.isInMorningSession) {
            $wire.submitMorningCheckOut();
        } else if (this.isInAfternoonSession) {
            $wire.submitAfternoonCheckOut();
        }
    },
    
    remainingHoursInSeconds: {{ auth()->user()->remaining_hours * 3600 ?? 0 }}, // Convert initial hours to seconds
    countdownInterval: null,
    databaseUpdateInterval: null,
    isCountingDown: false,

    formatTime(totalSeconds) {
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;
        const formattedHours = String(hours).padStart(3, '0');
        const formattedMinutes = String(minutes).padStart(2, '0');
        const formattedSeconds = String(seconds).padStart(2, '0');
        return `${formattedHours}:${formattedMinutes}:${formattedSeconds}`;
    },

    startCountdown() {
        if (this.isCountingDown) return;
        this.isCountingDown = true;
        this.countdownInterval = setInterval(() => {
            if (this.remainingHoursInSeconds > 0) {
                this.remainingHoursInSeconds = Math.max(0, this.remainingHoursInSeconds - 1);
            } else {
                clearInterval(this.countdownInterval);
                this.isCountingDown = false;
                // Optionally dispatch an event or update UI when hours reach zero
            }
        }, 1000);

        this.databaseUpdateInterval = setInterval(() => {
            if (this.isCountingDown) {
                $wire.updateRemainingHours(this.remainingHoursInSeconds / 3600); // Convert back to hours for database
            }
        }, 10000); // Update database every 10 seconds
    },
    
    stopCountdown() {
        clearInterval(this.countdownInterval);
        clearInterval(this.databaseUpdateInterval);
        this.isCountingDown = false;
    },

    init() {
        // Force refresh when data changes
        Livewire.on('logUpdated', () => {
            this.$nextTick(() => {
                console.log('Log updated event received, refreshing Alpine data');
            });
        });
        
        window.addEventListener('startCountdown', () => {
            this.startCountdown();
        });
        
        window.addEventListener('stopCountdown', () => {
            this.stopCountdown();
        });

        this.updateDateTime(); // Initial call to set the date and time
        setInterval(() => {
            $wire.checkLocation();
            $wire.checkSchedule();
            this.remainingHours = {{ auth()->user()->course->remaining_hours ?? 0 }};
            $wire.loadTodayLogTimes();
            $wire.loadTodayLog(); // Added to ensure todayLog is always up to date
            this.updateDateTime(); // Update date and time every second
        }, 1000);
    },
    
    updateDateTime() {
        const now = new Date();
        const options = { weekday: 'short', month: 'long', day: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric', second: 'numeric', hour12: true };
        this.liveDateTime = now.toLocaleDateString('en-US', options);
    },

    initSignaturePad() {
        // Only initialize if signature is needed
        if (!$wire.signatureNeeded) return;
        
        this.canvasRef = this.$refs.canvas;
        if (!this.canvasRef) {
            console.error('Canvas reference not found');
            return;
        }
        
        console.log('Initializing signature pad');
        
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        this.canvasRef.width = 300 * ratio;
        this.canvasRef.height = 150 * ratio;
        this.canvasRef.getContext('2d').scale(ratio, ratio);

        if (window.SignaturePad) {
            if (this.signaturePad) {
                this.signaturePad.off();
            }
            this.signaturePad = new window.SignaturePad(this.canvasRef, {
                backgroundColor: '#a7f3d0',
                penColor: 'black',
            });

            // Optional: prevent touch issues
            this.canvasRef.addEventListener('touchstart', (event) => {
                event.preventDefault();
                if (event.touches.length > 1) return;
                this.signaturePad._handleTouchStart(event);
            });
            this.canvasRef.addEventListener('touchmove', (event) => {
                event.preventDefault();
                if (event.touches.length > 1) return;
                this.signaturePad._handleTouchMove(event);
            });
            this.canvasRef.addEventListener('touchend', (event) => {
                event.preventDefault();
                this.signaturePad._handleTouchEnd(event);
            });

        } else {
            console.error('SignaturePad not found!');
        }
    }
}"

    x-init="
        // In your Alpine component
        Alpine.effect(() => {
            inside = $wire.isInside;
            console.log('Alpine effect running - todayLog:', $wire.todayLog);
            console.log('canCheckOut computed:', this.canCheckOut);
            console.log('isCheckInEnabled:', this.isCheckInEnabled);
        });
        setInterval(() => { $wire.checkLocation() }, 1000);

        let map = L.map('map').setView([{{ $latitude ?? 51.505 }}, {{ $longitude ?? -0.09 }}], 13); 

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_labels_under/{z}/{x}/{y}{r}.png', {
            attribution: '© <a href=\'https://www.openstreetmap.org/copyright\'>OpenStreetMap</a> contributors © <a href=\'https://carto.com/attributions\'>CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        let userMarker = L.marker([{{ $latitude ?? 51.505 }}, {{ $longitude ?? -0.09 }}]).addTo(map)
            .bindPopup('Your Location')
            .openPopup();

        let agencyMarker = L.marker([{{ $agencyLatitude ?? 51.5074 }}, {{ $agencyLongitude ?? -0.1278 }}]).addTo(map)
            .bindPopup('Agency Location')
            .openPopup();

        // Routing setup using Leaflet Routing Machine
        const routeControl = L.Routing.control({
            waypoints: [
                L.latLng({{ $latitude ?? 51.505 }}, {{ $longitude ?? -0.09 }}), // User's Location
                L.latLng({{ $agencyLatitude ?? 51.5074 }}, {{ $agencyLongitude ?? -0.1278 }}), // Agency's Location
            ],
            routeWhileDragging: true
        }).addTo(map);
        map.invalidateSize();
    "
>
    <div x-data="{ show: false }" x-init="setTimeout(() => show = true, 200)" x-show="show"
     x-transition:enter="transform transition ease-in-out duration-500"
     x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
     class="absolute bottom-0 w-full bg-white rounded-t-[2rem] shadow-lg p-6 flex flex-col gap-4 h-[90vh]"
     style="z-index: 50;">
         <div class="overflow-auto h-full">
        <div class="flex justify-between items-center mt-4">
              <h2 class="text-lg font-semibold text-gray-800">Attendance Portal</h2>
              <div class="flex items-center border rounded-full px-3 py-1 bg-white shadow">
                  <i class="fas fa-calendar-alt text-emerald-500 mr-2"></i>
                  <span class="text-gray-700" x-text="liveDateTime"></span>
              </div>
          </div>

       <div class="overflow-x-auto whitespace-nowrap border-b border-gray-200 mb-4 px-2 py-2">
            @foreach ($allDates as $date)
                @php
                    $isPast = in_array($date, $pastDates);
                    $isFuture = in_array($date, $futureDates);
                    $isToday = $date === now(config('app.timezone'))->toDateString();
                @endphp
                <button
                    wire:click="{{ $isFuture ? '' : "selectDate('$date')" }}"
                    class="inline-block text-center rounded-md mx-1 px-3 py-2 text-sm font-medium
                        {{ $selectedDate === $date ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-800' }}
                        {{ $isFuture ? 'cursor-not-allowed opacity-50' : 'hover:bg-blue-100' }}"
                    @disabled($isFuture)
                >
                    {{ \Carbon\Carbon::parse($date)->format('M d') }}
                </button>
            @endforeach
        </div>


        <div class="flex justify-between gap-4 mt-4">
            <div class="bg-sky-100 rounded-2xl p-5 w-1/2 text-center relative h-[40%]">
              <i class="fas fa-ellipsis-h absolute top-2 right-2 text-gray-500"></i>
              <div class="flex justify-center my-3">
                  <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center">
                      <i class="fas fa-clock text-sky-500 text-5xl"></i>
                  </div>
              </div>
              <p class="text-gray-500 text-sm">Total Hours Remaining</p>
                <p class="text-black font-bold text-3xl" x-text="formatTime(remainingHoursInSeconds)"></p>
                <button class="bg-sky-500 text-white font-bold py-2 px-4 rounded-full mt-3">View Details</button>
            </div>
          

            <div class="bg-orange-100 rounded-2xl p-5 w-1/2 text-center relative h-[40%]">
                <i class="fas fa-ellipsis-h absolute top-2 right-2 text-gray-500"></i>
                <div class="flex justify-center my-3">
                    <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center">
                        <i class="fas fa-sign-out-alt text-orange-500 text-5xl"></i>
                    </div>
                </div>
                <p class="text-gray-500 text-sm">Active Sessions</p>
                <p class="text-black font-bold text-3xl" id="active-sessions" x-text="$wire.activeSessions"></p>
                <p class="text-sm mt-2" :class="{ 'text-red-500': canCheckOut, 'text-gray-500': !canCheckOut }">
                    <span x-text="isInMorningSession ? 'Morning session active' : 
                                 (isInAfternoonSession ? 'Afternoon session active' : 'No active sessions')"></span>
                </p>
            </div>
        </div>
        
       <div class="grid grid-cols-2 gap-4 my-4">
              <div class="bg-white rounded-xl shadow-md p-4 flex flex-col justify-center items-center text-center border border-gray-100">
                  <div class="text-emerald-500 text-xl mb-2"><i class="fas fa-sun"></i></div>
                  <h3 class="font-semibold text-gray-700">Morning Check-in</h3>
                  <p class="text-gray-500 text-sm">{{ $morningInTime ?? 'Not Checked In' }}</p>
              </div>
              <div class="bg-white rounded-xl shadow-md p-4 flex flex-col justify-center items-center text-center border border-gray-100">
                  <div class="text-red-500 text-xl mb-2"><i class="fas fa-sign-out-alt"></i></div>
                  <h3 class="font-semibold text-gray-700">Morning Check-out</h3>
                  <p class="text-gray-500 text-sm">{{ $morningOutTime ?? '--:-- --' }}</p>
              </div>
              <div class="bg-white rounded-xl shadow-md p-4 flex flex-col justify-center items-center text-center border border-gray-100">
                  <div class="text-sky-500 text-xl mb-2"><i class="fas fa-cloud-sun"></i></div>
                  <h3 class="font-semibold text-gray-700">Afternoon Check-in</h3>
                  <p class="text-gray-500 text-sm">{{ $afternoonInTime ?? '--:-- --' }}</p>
              </div>
              <div class="bg-white rounded-xl shadow-md p-4 flex flex-col justify-center items-center text-center border border-gray-100">
                  <div class="text-red-600 text-xl mb-2"><i class="fas fa-moon"></i></div>
                  <h3 class="font-semibold text-gray-700">Afternoon Check-out</h3>
                  <p class="text-gray-500 text-sm">{{ $afternoonOutTime ?? '--:-- --' }}</p>
              </div>
          </div>
        <div>
    @if ($schedule)
        <div class="mt-4 p-4 bg-gray-100 rounded-md">
            <h4 class="font-semibold text-gray-700 mb-2">Loaded Schedule Details:</h4>
            <p><strong>Day of Week:</strong> {{ $schedule->day_of_week }}</p>
            <p><strong>Expected Morning Check-in:</strong> {{ $schedule->expected_morning_in ?? 'Not Set' }}</p>
            <p><strong>Expected Afternoon Check-in:</strong> {{ $schedule->expected_afternoon_in ?? 'Not Set' }}</p>
            <p><strong>Late Tolerance (minutes):</strong> {{ $schedule->late_tolerance ?? 'Not Set' }}</p>
            <p><strong>Agency ID:</strong> {{ $schedule->agency_id ?? 'Not Set' }}</p>
        </div>
    @else
        <p class="mt-4 text-red-500">No schedule loaded for today.</p>
    @endif
    
    <p><strong>Check-in Enabled:</strong> <span x-text="isCheckInEnabled ? 'Yes' : 'No'"></span></p>
    <p><strong>Check-out Enabled:</strong> <span x-text="canCheckOut ? 'Yes' : 'No'"></span></p>
        <p><strong>is inside Geofence:</strong> <span x-text="inside ? 'Yes' : 'No'"></span></p>
    
    <div>
    <p>Server Time (PHT): {{ $serverTime }}</p>
    </div>

    </div>
        
        

        <div class="relative mb-28 w-full h-[40%] bg-gray-200 rounded-2xl overflow-hidden" style="z-index: 1;">
          <div class="absolute top-2 left-1/2 transform -translate-x-1/2 w-[80%] bg-white rounded-full p-2 flex items-center shadow-md" style="z-index: 102;">
              <i class="fas fa-map-marker-alt text-emerald-500 text-lg ml-3"></i>
              <div class="ml-3">
                  <p class="text-gray-500 text-xs">Current Location</p>
                  <p class="text-black font-bold text-sm" wire:key="location-name">{{ $locationName }}</p>
              </div>
          </div>
      
          <div class="absolute inset-0 h-full w-full" style="z-index: 2;" wire:ignore id="map"></div>
      </div>

     <button
        class="absolute bottom-24 left-0 w-full font-bold py-3 rounded-full shadow-lg text-lg transition-all duration-300 z-20"
        :class="buttonClass"
        :disabled="isButtonDisabled"
        @click="handleButtonClick()"
        style="z-index: 101;"
    >
        <span x-text="buttonText"></span>
    </button>



<div
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    x-show="showSignatureModal"
    x-transition
    x-effect="if (showSignatureModal) { $nextTick(() => initSignaturePad()) }"
>
    <div class="relative bg-white rounded-xl shadow-lg p-6 flex flex-col w-[80vw] max-w-md">
        <!-- Debug info (optional, remove in production) -->
        <div class="bg-gray-100 p-2 mb-4 text-xs rounded">
            <p>Debug: signatureNeeded = {{ $signatureNeeded ? 'true' : 'false' }}</p>
            <p>Has todayLog = {{ $todayLog ? 'true' : 'false' }}</p>
        </div>
        
        <div>
            <template x-if="$wire.signatureNeeded">
                <div>
                    <h2 class="text-xl font-semibold mb-4 text-center text-emerald-700">Sign to Check In</h2>
                    <div class="relative w-full rounded-md overflow-hidden bg-emerald-100 flex justify-center items-center">
                        <canvas x-ref="canvas" class="touch-manipulation"></canvas>
                    </div>
                    <button
                        type="button"
                        class="mt-2 px-4 py-2 rounded-md text-gray-600 bg-gray-200 hover:bg-gray-300 focus:outline-none focus:ring focus:ring-gray-300"
                        @click="if (signaturePad) signaturePad.clear()"
                    >
                        Clear
                    </button>
                </div>
            </template>
            
            <template x-if="!$wire.signatureNeeded">
                <div>
                    <h2 class="text-xl font-semibold mb-4 text-center text-emerald-700">Check In</h2>
                    <p class="text-center text-gray-600 py-4">
                        You've already signed for today. No signature needed for this check-in.
                    </p>
                </div>
            </template>
        </div>

        <div class="mt-4 flex justify-end gap-2">
            <button
                type="button"
                class="px-4 py-2 rounded-md text-gray-600 bg-gray-200 hover:bg-gray-300 focus:outline-none focus:ring focus:ring-gray-300"
                @click="showSignatureModal = false; if (signaturePad) signaturePad.off(); signaturePad = null;"
            >
                Cancel
            </button>
            <button
                type="button"
                :disabled="!inside || !isCheckInEnabled || ($wire.signatureNeeded && signaturePad && signaturePad.isEmpty())"
                class="px-4 py-2 rounded-md text-white bg-emerald-500 hover:bg-emerald-600 focus:outline-none focus:ring focus:ring-emerald-300"
                @click="$wire.submitCheckIn($wire.signatureNeeded ? signaturePad.toDataURL() : null); showSignatureModal = false; if (signaturePad) signaturePad.off(); signaturePad = null;"
            >
                Submit
            </button>
        </div>
    </div>
</div>


    </div>

    
</div>
<style>
  button[disabled] {
    opacity: 0.7;
    cursor: not-allowed;
}

button:not([disabled]) {
    cursor: pointer;
    transition: all 0.3s ease;
}
</style>
</div>