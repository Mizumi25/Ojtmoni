<div class="p-6 w-full max-w-full flex gap-4 overflow-hidden"
     x-data="{ activeTab: 'agencies', open: false, selectedAgency: { agency_name: '', latitude: @entangle('selectedAgency.location.latitude').defer,
         longitude: @entangle('selectedAgency.location.longitude').defer, location: { location_name: '' } }, selectedRequest: { agency_name: '', location: { latitude: null, longitude: null, location_name: '' }}, map: null, marker: null, touchStartTime: 0, isTap: false, isViewingRequest: false, isDragging: false,
    handleDrop(event) {
        const files = event.dataTransfer.files;
        if (files.length > 0) {
            @this.set('image', files[0]);
        }
    },
    handleFileSelect(event) {
        const file = event.target.files[0];
        if (file) {
        
        }
    } }"
     x-init="
         $watch('open', value => {
             if (value && activeTab === 'agencies' && !map) {
                 initializeMap();
             } else if (value && map && activeTab === 'agencies' && selectedAgency.location.latitude && selectedAgency.location.longitude) {
                 updateMarker(selectedAgency.location.latitude, selectedAgency.location.longitude, selectedAgency.agency_name);
             } else if (!value && map && marker) {
                 map.removeLayer(marker);
                 marker = null;
             }
         });

         $watch('selectedAgency.location.latitude', value => {
             if (activeTab === 'agencies' && map && value !== null && selectedAgency.location.longitude !== null) {
                 updateMarker(value, selectedAgency.location.longitude, selectedAgency.agency_name);
             }
         });

         $watch('selectedAgency.location.longitude', value => {
             if (activeTab === 'agencies' && map && value !== null && selectedAgency.location.latitude !== null) {
                 updateMarker(selectedAgency.location.latitude, value, selectedAgency.agency_name);
             }
         });

         function initializeMap() {
             if (document.getElementById('map-container')) {
                 map = L.map('map-container').setView([8.8883, 125.1450], 13);
                 L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
                 if (selectedAgency.location.latitude && selectedAgency.location.longitude) {
                     updateMarker(selectedAgency.location.latitude, selectedAgency.location.longitude, selectedAgency.agency_name);
                 }

                 map.on('touchstart', function(e) {
                     touchStartTime = Date.now();
                     isTap = true;
                 });

                 map.on('touchmove', function(e) {
                     isTap = false;
                 });

                 map.on('touchend', function(e) {
                     if (isTap && Date.now() - touchStartTime < 300) { // Adjust delay (300ms) as needed
                         const touch = e.changedTouches[0];
                         const clickEvent = new MouseEvent('click', {
                             clientX: touch.clientX,
                             clientY: touch.clientY,
                             bubbles: true,
                             cancelable: true,
                             view: window
                         });
                         e.target.dispatchEvent(clickEvent);
                     }
                 });

                 map.on('click', function(e) {
                     const lat = e.latlng.lat;
                     const lng = e.latlng.lng;

                     selectedAgency.location.latitude = lat;
                     selectedAgency.location.longitude = lng;

                     // Manually sync with Livewire
                     $wire.set('selectedAgency.location.latitude', lat);
                     $wire.set('selectedAgency.location.longitude', lng);

                     reverseGeocode(lat, lng);
                 });
             }
         }

         function reverseGeocode(lat, lng) {
          fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
              .then(response => response.json())
              .then(data => {
                  const locationName = data.display_name || 'Unknown Location';
                  selectedAgency.location.location_name = locationName;
      
                  // Sync to Livewire
                  $wire.set('selectedAgency.location.location_name', locationName);
              })
              .catch(error => {
                  console.error('Reverse geocoding failed:', error);
              });
      }


         function updateMarker(latitude, longitude, agencyName) {
             const latLng = [parseFloat(latitude), parseFloat(longitude)];
             if (marker) {
                 marker.setLatLng(latLng).bindPopup(agencyName).openPopup();
             } else {
                 marker = L.marker(latLng).addTo(map).bindPopup(agencyName).openPopup();
             }
             map.setView(latLng, 15); // Set the view

             // Force Leaflet to recalculate its size and redraw
             if (map) {
                 setTimeout(() => {
                     map.invalidateSize();
                 }, 10); // Small delay to ensure DOM has updated
             }

             // Optional: Add or update the circle (geofence)
             map.eachLayer(layer => {
                 if (layer instanceof L.Circle) {
                     map.removeLayer(layer);
                 }
             });
             L.circle(latLng, {
                 radius: parseFloat(selectedAgency.agency_radius) || 100,
                 color: '#3182ce',
                 fillColor: '#90cdf4',
                 fillOpacity: 0.3
             }).addTo(map);
         }

         document.addEventListener('livewire:load', () => {
             Livewire.on('agencySelected', (agency) => {
                 selectedAgency = agency;
                 activeTab = 'agencies'; 
                 open = true; // Ensure the panel is open
                 isViewingRequest = false; 
                 // The $watch('selectedAgency') will handle the map and marker update
             });
             Livewire.on('agencyRequestSelected', (request) => { 
                 selectedRequest = request; 
                 activeTab = 'requests'; 
                 open = true; // Ensure the panel is open 
                 isViewingRequest = true; 
             }); 
         });
        "
>
    <div :class="open ? 'flex-[2] w-2/3' : 'flex-1 w-full'" class="transition-all duration-300">
        <nav class="text-gray-600 mb-4">
            <span class="text-gray-500">Records</span> >
            <span class="text-gray-800" x-text="activeTab === 'agencies' ? 'Support Agencies' : 'Agency Requests'"></span>
        </nav>

        <div class="flex justify-between items-cente flex-col mb-4">
          <div class="flex justify-between items-center mb-4">
    <div>
        <h1 class="text-2xl font-semibold text-gray-800" x-text="activeTab === 'agencies' ? 'Support Agency Management' : 'Agency Requests'"></h1>
        <p class="text-gray-500 text-sm" x-text="activeTab === 'agencies' ? 'Manage partner companies and their internship availability.' : 'Review and process new agency requests.'"></p>
            </div>
          <div>
              <button
                  @click="
                      open = true;
                      isViewingRequest = false;
                      selectedAgency = {
                          id: null,
                          agency_name: '',
                          location: { latitude: null, longitude: null, location_name: '' },
                          agency_background: '',
                          agency_number: '',
                          slot: '',
                          agency_radius: ''
                      };
                      if (marker) {
                          map.removeLayer(marker);
                          marker = null;
                      }
                  "
                  wire:click="resetSelectedAgency()"
                  class="bg-emerald-500 text-white hover:bg-emerald-700 font-bold py-2 px-4 rounded"
              >
                  Add Agency
              </button>
          </div>
        </div>
        
        <div class="flex space-x-4 mb-4">
            <button
                @click="activeTab = 'agencies'; isViewingRequest = false; open = false;"
                :class="{'border-b-2 border-emerald-600 text-emerald-500': activeTab === 'agencies', 'bg-white text-gray-700': activeTab !== 'agencies'}"
                class="px-4 py-2 transition"
            >
                Agencies
            </button>
            <button
                @click="activeTab = 'requests'; isViewingRequest = true; open = false;"
                :class="{'border-b-2 border-emerald-600 text-emerald-500': activeTab === 'requests', 'bg-white text-gray-700': activeTab !== 'requests'}"
                class="px-4 py-2 transition"
            >
                Agency Requests
            </button>
        </div>
        </div>

        <div class="bg-white rounded-md p-4 overflow-x-auto"   x-show="activeTab === 'agencies'">
    <div class="grid grid-cols-[auto_minmax(0,1fr)_auto] gap-4 items-center mb-2 font-semibold text-gray-700">
        <span></span>
        <div class="grid grid-cols-5 gap-2">
            <span>Name</span>
            <span>Number</span>
            <span>Contact</span>
            <span>Slots</span>
            <span>Status</span>
        </div>
        <span class="text-right">Actions</span>
    </div>
    @foreach ( $agencies as $agency)
    <div class="grid grid-cols-[auto_minmax(0,1fr)_auto] gap-4 items-center py-4 border-b last:border-none cursor-pointer hover:bg-gray-100 transition"
         @click="open = true; isViewingRequest = false; selectedAgency = {
             id: '{{ $agency->id }}',
             agency_name: '{{ e($agency->agency_name) }}',
             agency_number: '{{ e($agency->agency_number) }}',
             agency_background: '{{ e($agency->agency_background) }}',
             agency_radius: '{{ e($agency->agency_radius) }}',
             slot: '{{ $agency->slot }}',
             location: {
                 latitude: {{ $agency->location->latitude ?? 'null' }},
                 longitude: {{ $agency->location->longitude ?? 'null' }},
                 location_name: '{{ e($agency->location->location_name ?? '') }}'
             }
         };"
         wire:click="selectAgency({{ $agency->id }});"
    >
        <div class="w-12 h-12 rounded-full overflow-hidden bg-emerald-200 flex items-center justify-center">
            @if (!empty($agency->agency_image))
                <img src="{{ asset('storage/' . $agency->agency_image) }}" alt="{{ $agency->agency_name }}" class="w-full h-full object-cover">
            @else
                <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 22V12h6v10"></path>
                </svg>
            @endif
        </div>
        <div class="grid grid-cols-5 gap-2 items-center">
            <span class="text-gray-800">{{ $agency->agency_name }}</span>
            <span class="text-gray-500 text-sm">{{ $agency->agency_number }}</span>
            <span class="text-emerald-600 text-sm underline cursor-pointer">{{ $agency->contactPerson->name }}</span>
            <span class="text-gray-700">{{ $agency->slot }}</span>
            <div class="flex items-center">
                <div class="flex items-center px-2 py-1 border rounded-md text-gray-700 text-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 mr-1"></span> MOA soon
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2 justify-end">
            <button wire:click="goToTrackingMap({{ $agency->id }})" class="text-emerald-500 hover:text-emerald-700 font-bold py-2 px-3 rounded">
                <i class="fas fa-map-marked-alt"></i>
            </button>
          </button>
        </div>
    </div>
    @endforeach
</div>

        <div class="bg-white rounded-md p-4 overflow-x-auto grid-cols-2" x-show="activeTab === 'requests'">
            @foreach ( $agencyRequests as $request)
            <div
                class="grid grid-cols-[1.5fr_1fr_1fr_auto] items-center py-4 border-b last:border-none cursor-pointer hover:bg-gray-100 transition"
                @click="open = true; isViewingRequest = true; selectedRequest = {
                    id: '{{ $request->id }}',
                    agency_name: '{{ $request->agency_name }}',
                    agency_background: '{{ $request->agency_background }}',
                    location: {
                        latitude: {{ $request->location->latitude ?? 'null' }},
                        longitude: {{ $request->location->longitude ?? 'null' }},
                        location_name: '{{ $request->location->location_name ?? '' }}'
                    },
                    requested_by_user_id: '{{ $request->requested_by_user_id }}',
                    requester_name: '{{ $request->requester->name ?? 'N/A' }}',
                    requester_course: '{{ $request->requester->course->abbreviation ?? 'N/A' }}',
                };"
                wire:click="selectAgencyRequest({{ $request->id }});"
            >
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"></path>
                        </svg>
                    </div>
                    <span class="text-gray-800 font-semibold">{{ $request->agency_name }}</span>
                </div>
                <div class="text-gray-500 text-sm">{{ Str::limit($request->agency_background, 30) }}</div>
                <div class="text-emerald-600 text-sm underline cursor-pointer">{{ $request->requester->name ?? 'N/A' }} (Department Coordinator: {{ $request->requester->course->abbreviation }})</div>
                <div>
                   
                </div>
            </div>
            @endforeach
        </div>
    </div>

    
    <div id="right-panel" x-show="open" class="flex-[1] bg-white p-4 rounded shadow transition-all duration-300 relative">
      <button
        @click="open = false; selectedAgency = { id: null, agency_name: '', agency_background: '', agency_number: '', slot: '', agency_radius: '', location: { location_name: '', latitude: null, longitude: null } }"
        class="absolute top-4 right-4 text-gray-600 hover:text-gray-800"
    >
        <i class="fas fa-times fa-lg"></i>
    </button>
    <h2 class="text-xl font-bold mb-4" x-text="isViewingRequest ? 'Agency Request Details' : (selectedAgency.id ? 'Edit Agency' : 'Add Agency')"></h2>

        <template x-if="isViewingRequest">
            <div>
                <div class="mb-3">
                    <label class="block text-sm text-gray-600">Agency Name</label>
                    <input type="text" x-model="selectedRequest.agency_name" class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-3">
                    <label class="block text-sm text-gray-600">Background</label>
                    <textarea x-model="selectedRequest.agency_background" class="w-full border rounded px-3 py-2"></textarea>
                </div>

                <div class="mb-3">
                    <label class="block text-sm text-gray-600">Requested By</label>
                    <input type="text" :value="selectedRequest.requester_name+ ' (Department: ' + selectedRequest.requester_course + ')'" class="w-full border rounded px-3 py-2" readonly>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div>
                        <label class="block text-sm text-gray-600">Longitude</label>
                        <input type="text" x-model.number="selectedRequest.location.longitude" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600">Latitude</label>
                        <input type="text" x-model.number="selectedRequest.location.latitude" class="w-full border rounded px-3 py-2">
                    </div>
                </div>
                
                <div>
                     <label class="block text-sm text-gray-600">Location</label>
                     <input type="text" x-model.number="selectedRequest.location.location_name" class="w-full border rounded px-3 py-2">
                 </div>
                 
                <div class="flex gap-2">
                    <button @click="open = false" wire:click="approveAgencyRequest({{ $selectedRequest['id'] }})" class="bg-emerald-500 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded">
                        Approve
                    </button>
                    <button @click="open = false" wire:click="disapproveAgencyRequest({{ $selectedRequest['id'] }})" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Disapprove
                    </button>
                </div>
            </div>
        </template>
    
       <template x-if="!isViewingRequest">
        <div>
           <div
                  @click="$refs.fileInput.click()"
                  class="w-full border-2 border-emerald-500 rounded-md p-6 flex items-center justify-center bg-gradient-to-b from-emerald-200 to-emerald-250 bg-opacity-50 backdrop-filter backdrop-blur-md cursor-pointer relative"
                  style="min-height: 150px;"
                  @dragenter.prevent="isDragging = true"
                  @dragleave.prevent="isDragging = false"
                  @dragover.prevent
                  @drop.prevent="isDragging = false; handleDrop($event)"
                  :class="{'border-dashed': isDragging}"
              >
                  {{-- Image Preview Inside Box --}}
                  @if ($image)
                      <div class="w-full h-full relative flex items-center justify-center">
                          <img src="{{ $image->temporaryUrl() }}" alt="New Image" class="w-full h-full object-cover rounded-md">
                          <button wire:click="$set('image', null)" type="button"
                                  class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 z-10">
                              <i class="fas fa-times"></i>
                          </button>
                      </div>
                  @elseif (!empty($selectedAgency['agency_image']))
                      <div class="w-full h-full relative flex items-center justify-center">
                          <img src="{{ asset('storage/' . $selectedAgency['agency_image']) }}" alt="Current Agency Image" class="w-full h-full object-cover rounded-md">
                      </div>
                  @else
                      {{-- Default Icon and Instruction --}}
                      <div class="flex flex-col items-center justify-center">
                          <div class="w-16 h-16 rounded-full bg-emerald-500 flex items-center justify-center transform rotate-12">
                              <i class="far fa-images fa-2x text-white"></i>
                          </div>
                          <p class="mt-2 text-gray-500 text-sm text-center">Drag and drop your image here, 2MB max</p>
                      </div>
                  @endif
              
                  <input type="file" wire:model="image" class="hidden" x-ref="fileInput" accept="image/*">
              </div>
              
              @error('image')
                  <span class="text-red-500 text-xs">{{ $message }}</span>
              @enderror

                      
            <div class="mb-3">
                <label class="block text-sm text-gray-600">Agency Name</label>
                <input type="text" wire:model.defer="selectedAgency.agency_name" class="w-full border rounded px-3 py-2">
            </div>
    
            <div class="mb-3">
                <label class="block text-sm text-gray-600">Background</label>
                <textarea wire:model.defer="selectedAgency.agency_background" class="w-full border rounded px-3 py-2"></textarea>
            </div>
    
            <div class="mb-3">
                <label class="block text-sm text-gray-600">Contact Number</label>
                <input type="text" wire:model.defer="selectedAgency.agency_number" class="w-full border rounded px-3 py-2">
            </div>
    
            <div class="grid grid-cols-2 gap-4 mb-3">
                <div>
                    <label class="block text-sm text-gray-600">Longitude</label>
                    <input type="text" wire:model.defer="selectedAgency.location.longitude" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm text-gray-600">Latitude</label>
                    <input type="text" wire:model.defer="selectedAgency.location.latitude" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm text-gray-600">Location</label>
                    <input type="text" wire:model.defer="selectedAgency.location.location_name" class="w-full border rounded px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm text-gray-600">Radius</label>
                    <input type="number" wire:model.defer="selectedAgency.agency_radius" class="w-full border rounded px-3 py-2" />
                </div>
            </div>
    
            <div wire:ignore id="map-container" class="w-full h-64 mb-4 rounded bg-gray-100" style="z-index: 102;"></div>
    
            <div class="mb-3">
                <label class="block text-sm text-gray-600">Available Slots</label>
                <input type="number" wire:model.defer="selectedAgency.slot" class="w-full border rounded px-3 py-2">
            </div>
    
            <div class="flex flex-roe items-center justify-between">
              <template x-if="selectedAgency.id">
                <button wire:click="saveAgency()" @click="open = false"class="bg-emerald-500 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded">
                    Update Agency
                </button>
                </template>
                <template x-if="!selectedAgency.id">
                    <button wire:click="createAgency()" @click="open = false" class="bg-emerald-500 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded">
                        Add Agency
                    </button>
                </template>
                <template x-if="selectedAgency.id">
                  <button wire:click="deleteAgency({{ $selectedAgency['id'] }})" @click="open = false" class="text-white bg-red-400 hover:text-ruby-700 font-bold py-2 px-4 rounded flex items-center gap-2">
                      <i class="fas fa-trash"></i> Delete
                  </button>
              </template>
            </div>
        </div>
    </template>

    
        @if (session()->has('message'))
            <div class="mt-4 text-green-500">
                {{ session('message') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="mt-4 text-red-500">
                {{ session('error') }}
            </div>
        @endif
    </div>
</div>