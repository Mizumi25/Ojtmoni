<div class="h-screen p-4 bg-gray-100 w-full" x-data="{
    search: '',
    showRequest: false,
    selectedCompany: null,
    activeTab: 'approved',
    map: null,
    marker: null,
    latitude: @entangle('latitude').live,
    longitude: @entangle('longitude').live,
    locationName: @entangle('locationName').live, // Entangle locationName
    reverseGeocode() {
        if (this.latitude && this.longitude) {
            fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${this.latitude}&lon=${this.longitude}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.display_name) {
                        this.locationName = data.display_name;
                    } else {
                        this.locationName = ''; // Or a default message
                    }
                })
                .catch(error => {
                    console.error('Error fetching reverse geocode:', error);
                    this.locationName = 'Could not determine location'; // Or a user-friendly error
                });
        } else {
            this.locationName = '';
        }
    },
    initMap() {
        this.map = L.map('map-container').setView([8.8883, 125.1450], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(this.map);

        this.map.on('click', (e) => {
            this.latitude = e.latlng.lat;
            this.longitude = e.latlng.lng;
            this.updateMarker(this.latitude, this.longitude);
            this.reverseGeocode(); // Perform reverse geocode on map click
        });

        if (this.latitude && this.longitude) {
            this.updateMarker(this.latitude, this.longitude);
            this.reverseGeocode(); // Perform initial reverse geocode if coordinates are present
        }
    },
    updateMarker(lat, lng) {
        const latLng = [lat, lng];
        if (this.marker) {
            this.marker.setLatLng(latLng);
        } else {
            this.marker = L.marker(latLng).addTo(this.map);
        }
        this.map.setView(latLng, 15);
    },
    syncCoordinates() {
        if (this.latitude && this.longitude && this.map) {
            this.updateMarker(this.latitude, this.longitude);
            this.reverseGeocode(); // Perform reverse geocode when coordinates change
        }
    }
}" x-init="initMap(); $watch('latitude', () => syncCoordinates()); $watch('longitude', () => syncCoordinates());">
    <nav class="text-sm text-gray-500 mb-4 flex items-center">
        <span class="text-blue-600">Companies</span>
        <span class="mx-2"> > </span>
        <span class="text-gray-700 font-medium" x-text="selectedCompany ? selectedCompany.agency_name : 'List'"></span>
    </nav>
    @if (session()->has('message'))
            <div class="mt-4 p-4 bg-green-100 text-green-800 rounded">
                {{ session('message') }}
            </div>     @endif

    <div class="flex justify-between items-center mb-4">
        <div class="relative mb-4">
            <input
                type="text"
                class="w-full pl-4 pr-10 py-3 rounded-full bg-gray-100 text-gray-700 focus:ring focus:ring-emerald-300 focus:outline-none border-2 border-emerald-300"
                placeholder="Search..."
                x-model="search"
            />
        </div>
        <button @click="showRequest = true; selectedCompany = null"
                class="bg-emerald-600 text-white px-4 py-2 rounded-full shadow-md hover:bg-emerald-700">
            <i class="fas fa-plus-circle mr-2"></i> Request Company
        </button>
    </div>

    <div class="mb-4">
        <button @click="activeTab = 'approved'" :class="{'border-b-2 border-emerald-500 text-emerald-700': activeTab === 'approved', 'text-gray-500': activeTab !== 'approved'}" class="px-4 py-2 rounded-l">Approved Companies</button>
        <button @click="activeTab = 'requested'" :class="{'border-b-2 border-emerald-500 text-emerald-700': activeTab === 'requested', 'text-gray-500': activeTab !== 'requested'}" class="px-4 py-2 rounded-r">My Requests</button>
    </div>

    <div class="flex space-x-4">
        <div class="w-full transition-all duration-300">
            <ul class="divide-y" x-show="activeTab === 'approved'">
                @foreach ($agencies as $agency)
                    <li class="p-4 flex justify-between items-center hover:bg-gray-50 cursor-pointer"
                        x-show="'{{ strtolower($agency->agency_name) }}'.includes(search.toLowerCase()) ||
                               '{{ strtolower($agency->agency_background) }}'.includes(search.toLowerCase())">
                        <div>
                            <p class="font-semibold">{{ $agency->agency_name }}</p>
                           <p class="text-sm text-gray-500">{{ $agency->agency_background }}</p>
                        </div>
                        <button @click='selectedCompany = @json($agency); showRequest = false'
                                    class="text-emerald-500 text-xl">
                            <i class="fas fa-info-circle"></i>
                        </button>
                    </li>
                @endforeach
            </ul>

            <ul class="divide-y" x-show="activeTab === 'requested'">
                @foreach ($requestedAgencies as $request)
                    <li class="p-4 flex justify-between items-center hover:bg-gray-50 cursor-pointer">
                        <div>
                            <p class="font-semibold">{{ $request->agency_name }}</p>
                           <p class="text-sm text-gray-500">Background: {{ $request->agency_background ?? 'N/A' }} | Requested on: {{ $request->created_at->format('Y-m-d') }}</p>
                           @if ($request->location)
                                <p class="text-xs text-gray-400">Lat: {{ $request->location->latitude }}, Lng: {{ $request->location->longitude }}</p>
                           @endif
                        </div>
                        <span class="text-gray-500 text-sm italic">Pending Approval</span>
                    </li>
                @endforeach
                @if ($requestedAgencies->isEmpty())
                    <li class="p-4 text-gray-500 italic">No pending requests.</li>
                @endif
            </ul>
        </div>

        <div x-show="selectedCompany" class="w-1/3 bg-white p-6 rounded shadow-lg transition-all duration-300">
            <h2 class="text-xl font-bold mb-2">Company Details</h2>
            <p class="text-gray-600 text-sm" x-text="selectedCompany.agency_name"></p>
            <p class="mt-4 text-gray-500 text-sm">Background: <span x-text="selectedCompany.agency_background || 'N/A'"></span></p>
            <p class="text-gray-500 text-sm">Status: <span x-text="selectedCompany.status || 'N/A'"></span></p>
            @if (isset($selectedCompany['location']))
                <div class="mt-4">
                    <p class="text-sm font-semibold mb-1">Location</p>
                    <p class="text-xs text-gray-600">Lat: {{ $selectedCompany['location']['latitude'] }}, Lng: {{ $selectedCompany['location']['longitude'] }}</p>
                </div>
            @endif
        </div>

        <div x-show="showRequest" class="w-1/3 bg-white p-6 rounded shadow-lg transition-all duration-300">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Request Company</h2>
                <button @click="showRequest = false" class="text-gray-500 text-xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium">Company Name</label>
                    <input type="text" wire:model="newCompanyName" class="w-full p-2 border-b border-gray-400 focus:outline-none focus:border-blue-500">
                </div>

                <div>
                    <label class="text-sm font-medium">Background</label>
                    <input type="text" wire:model="newCompanyBackground" class="w-full p-2 border-b border-gray-400 focus:outline-none focus:border-blue-500">
                </div>

               <div class="bg-gray-200 p-4 rounded shadow">
                    <p class="text-sm font-semibold mb-2">Location</p>
                    <div wire:ignore id="map-container" class="w-full h-64 mb-4 rounded bg-gray-100"></div>
                    <div class="flex space-x-2 mb-2">
                        <div>
                            <label class="text-xs font-medium">Latitude</label>
                            <input type="text" wire:model.live="latitude" class="w-24 p-1 border border-gray-400 rounded text-sm focus:outline-none focus:border-blue-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium">Longitude</label>
                            <input type="text" wire:model.live="longitude" class="w-24 p-1 border border-gray-400 rounded text-sm focus:outline-none focus:border-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-medium">Location Name</label>
                        <input type="text" wire:model.live="locationName" class="w-full p-1 border border-gray-400 rounded text-sm focus:outline-none focus:border-blue-500">
                    </div>
                </div>
                
                <div>
                    <label class="text-sm font-medium">Description/Reason</label>
                    <textarea wire:model="newCompanyDescription" class="w-full p-2 border border-gray-400 rounded focus:outline-none focus:border-blue-500" rows="3"></textarea>
                </div>

                <div class="text-right">
                    <button wire:click="requestCompany" class="bg-emerald-600 text-white px-4 py-2 rounded-full shadow-md hover:bg-emerald-700">
                        <i class="fas fa-paper-plane"></i> Send Request
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>