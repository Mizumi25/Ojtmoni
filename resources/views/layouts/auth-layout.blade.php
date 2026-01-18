<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Meta and Head -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'OJT Track') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>
<body class="font-sans text-gray-900 antialiased">

    @php
        $activeSemester = \App\Models\Semester::where('status', 'active')->first();
        $userCourseOfferingInActiveSemester = null;
        if (auth()->check() && auth()->user()->role === 'student' && auth()->user()->courseOffering) {
            if ($activeSemester && auth()->user()->courseOffering->semester_id === $activeSemester->id) {
                $userCourseOfferingInActiveSemester = true;
            }
        }
    @endphp

    @if (auth()->check() && auth()->user()->role === 'student' && (!$activeSemester || !auth()->user()->course_offering_id || !$userCourseOfferingInActiveSemester))
        <div class="flex items-center justify-center h-screen bg-gray-100">
            <div class="bg-white rounded-lg p-8 shadow-md text-center">
                @if (!$activeSemester)
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">No Active Semester</h2>
                    <p class="text-gray-600">Please wait for an active semester to begin.</p>
                @elseif (!auth()->user()->course_offering_id || !$userCourseOfferingInActiveSemester)
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">No Active Course Offering Assigned</h2>
                    <p class="text-gray-600">It seems you are not yet assigned to an active course offering for the current semester.</p>
                    <p class="text-gray-600">Please contact your coordinator for assistance.</p>
                @endif
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                 <button type="submit" class="border border-emerald-600 text-emerald-600 py-2 px-4 rounded-2xl hover:bg-emerald-600 hover:text-white transition duration-200">
                        Logout
                   </button>
              </form>
            </div>
        </div>
    @else
        <div class="flex h-full w-full {{ auth()->user()->role !== 'student' ? 'bg-gradient-to-b from-white to-emerald-50' : 'bg-black' }}">
            @if(Auth::user()->role !== 'student')
                <livewire:nav.side />
            @endif

            <div class="flex flex-col flex-1 h-full">
                @php
                    $user = auth()->user();
                @endphp

                @if($user->role !== 'student' || $user->status !== 'pending')
                    <livewire:nav.header />
                @endif

                <div class="flex-1 flex items-center justify-center w-full h-full">
                    {{ $slot }}
                </div>
            </div>
        </div>
    @endif

    @livewireScripts
    
    <div id="gps-overlay" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-2xl p-6 shadow-xl max-w-md w-full text-center flex flex-col justify-center items-center">
        <!-- Icon and Heading -->
        <div class="flex justify-center mb-4">
            <i class="fas fa-location-arrow text-8xl text-emerald-600"></i>
        </div>
        <h2 class="text-2xl font-semibold mb-2">Location Access Needed</h2>
        <p class="text-gray-600 mb-4">We need your location to continue. Please enable location services on your device.</p>

        <!-- Buttons -->
        <div class="flex justify-between gap-4 w-1/2">
            <!-- Logout Button (Left Side) -->
            <form method="POST" action="{{ route('logout') }}">
              @csrf
               <button type="submit" class="border border-emerald-600 text-emerald-600 py-2 px-4 rounded-2xl hover:bg-emerald-600 hover:text-white transition duration-200">
                      Logout
                 </button>
              </form>
            <!-- Open Location Settings Button -->
            <a href="javascript:void(0)" id="open-settings" class="bg-emerald-600 hover:bg-emerald-700 text-white py-2 px-4 rounded-2xl transition duration-200">
                Open Location
            </a>
        </div>
    </div>
</div>



    @if(auth()->check() && auth()->user()->role === 'student' && auth()->user()->status === 'intern')
<script>
const gpsOverlay = document.getElementById('gps-overlay');
const settingsBtn = document.getElementById('open-settings');

// Check if location is enabled
function checkGPSAccess() {
    navigator.geolocation.getCurrentPosition(
        () => gpsOverlay.classList.add('hidden'),
        (error) => {
            if (error.code === error.PERMISSION_DENIED || error.code === error.POSITION_UNAVAILABLE) {
                gpsOverlay.classList.remove('hidden');
            }
        },
        {
            enableHighAccuracy: true,
            timeout: 5000,
            maximumAge: 0
        }
    );
}

// Trigger initial check
checkGPSAccess();

// Re-check every 10 seconds (optional)
setInterval(checkGPSAccess, 10000);

// Anchor fallback (location settings)
settingsBtn.addEventListener('click', () => {
    const ua = navigator.userAgent.toLowerCase();

    navigator.geolocation.getCurrentPosition(
        (position) => {
            console.log("Location allowed:", position);
            // Maybe hide modal here
        },
        (err) => {
            // If denied, explain next steps
            if (ua.includes("android")) {
                alert("Please go to your phone's Settings > Location > Turn on location access.\n\nIf using Huawei, go to Settings > Privacy > Location Services.\n\nThen reload the page.");
            } else if (ua.includes("iphone") || ua.includes("ipad")) {
                alert("Go to: Settings > Privacy > Location Services and enable it.\n\nThen reload the page.");
            } else {
                alert("Please enable location in your browser or OS settings, then reload this page.");
            }
        }
    );
});





    

let lastLat = null;
let lastLong = null;
let lastSentTime = 0;
let isThrottled = false; // Flag to control throttling

// Utility to calculate distance between two coordinates in meters
function getDistanceFromLatLonInMeters(lat1, lon1, lat2, lon2) {
    const R = 6371e3; // Radius of Earth in meters
    const toRad = (val) => val * Math.PI / 180;

    const dLat = toRad(lat2 - lat1);
    const dLon = toRad(lon2 - lon1);
    const a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
        Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

if ('geolocation' in navigator) {
    navigator.geolocation.getCurrentPosition(async function(position) {
        const lat = position.coords.latitude;
        const long = position.coords.longitude;
        lastLat = lat;
        lastLong = long;
        lastSentTime = Date.now();

        try {
            const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${long}`);
            const data = await res.json();
            const locationName = data.display_name || 'Unknown Location';

            await fetch('/update-location', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    latitude: lat,
                    longitude: long,
                    location_name: locationName
                })
            });
        } catch (e) {
            console.error('Initial location error:', e);
        }
    });

    // Continuous watch with throttling mechanism
    navigator.geolocation.watchPosition(async function(position) {
        const lat = position.coords.latitude;
        const long = position.coords.longitude;
        const now = Date.now();
        const distanceMoved = lastLat && lastLong
            ? getDistanceFromLatLonInMeters(lastLat, lastLong, lat, long)
            : Infinity;

        // Throttle: Only allow updates if 5 seconds have passed or if distance moved is > 5 meters
        if ((distanceMoved > 60 || now - lastSentTime > 60000) && !isThrottled) {
            isThrottled = true; // Prevent further updates until throttled period is over
            lastLat = lat;
            lastLong = long;
            lastSentTime = now;

             if (lat && long) {
                  try {
                      const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${long}`);
                      const data = await res.json();
              
                      if (!data || !data.display_name) {
                          console.warn('Reverse geocode failed or empty display name');
                          return; // Do not proceed if no location name
                      }
              
                      const locationName = data.display_name || 'Unknown Location';
              
                      await fetch('/update-location', {
                          method: 'POST',
                          headers: {
                              'Content-Type': 'application/json',
                              'X-CSRF-TOKEN': '{{ csrf_token() }}'
                          },
                          body: JSON.stringify({
                              latitude: lat,
                              longitude: long,
                              location_name: locationName
                          })
                      });
                  } catch (e) {
                      console.error('Error in location update:', e);
                  }
              }


            // Set a timeout to allow for the next update
            setTimeout(() => {
                isThrottled = false; // Allow updates again after the throttle period
            }, 5000); // 5 seconds throttle period
        }
    }, function(error) {
        console.warn('Geolocation error:', error);
    }, {
        enableHighAccuracy: true,
        maximumAge: 10000,
        timeout: 10000
    });
}

</script>
@endif


    

</body>
</html>
