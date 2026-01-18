<div x-data="{
        maximized: false,
        map: null,
        studentMarkers: {},
        agencyMarkers: {},
        filter: 'all',
        filteredItems: [],
        isDropdownOpen: false,
        searchQuery: '',
        
        get filteredList() {
            return this.filteredItems.filter(item => {
                return item.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                       item.phone.toLowerCase().includes(this.searchQuery.toLowerCase());
            });
        },

        updateStudentMarker(user) {
            const latLng = [user.location.latitude, user.location.longitude];
        
            if (this.studentMarkers[user.id]) {
                this.studentMarkers[user.id].setLatLng(latLng);
            } else {
                const marker = L.marker(latLng)
                    .addTo(this.map)
                    .bindPopup(`<b>${user.name ?? 'Updated User'}</b><br>${user.location.location_name}`);
        
                marker._icon.style.filter = 'hue-rotate(90deg) saturate(1.5) brightness(1.1)';
                this.studentMarkers[user.id] = marker;
            }
        
            L.circle(latLng, {
                radius: 2,
                color: '#10b981',
                fillColor: '#34d399',
                fillOpacity: 0.3
            }).addTo(this.map);
        },
        
        focusOnItem(item) {
            if (item.isAgency) {
                // Find the agency in our data
                const agency = {{ Js::from($agencies) }}.find(a => a.id === item.id);
                if (agency && agency.location) {
                    this.map.setView(
                        [agency.location.latitude, agency.location.longitude], 
                        15
                    );
                }
            } else {
                // Find the student in our data
                const student = {{ Js::from($allStudents) }}.find(s => s.id === item.id);
                if (student && student.location) {
                    this.map.setView(
                        [student.location.latitude, student.location.longitude], 
                        15
                    );
                }
            }
        },
        
        updateFilteredItems() {
            if (this.filter === 'student') {
                this.filteredItems = {{ Js::from($allStudents->map(function($student) {
                    return [
                        'id' => $student->id,
                        'name' => $student->name,
                        'phone' => $student->phone_number,
                        'isAgency' => false,
                        'isOnline' => !is_null($student->location_id),
                        'profile_picture' => $student->profile_picture,
                        'location' => $student->location
                    ];
                })) }};
            } else if (this.filter === 'agency') {
                this.filteredItems = {{ Js::from($agencies->map(function($agency) {
                    return [
                        'id' => $agency->id,
                        'name' => $agency->agency_name,
                        'phone' => $agency->contactPerson ? $agency->contactPerson->phone_number : $agency->agency_number,
                        'isAgency' => true,
                        'isOnline' => true,
                        'profile_picture' => $agency->agency_image,
                        'location' => $agency->location
                    ];
                })) }};
            } else {
                this.filteredItems = [
                    ...{{ Js::from($allStudents->map(function($student) {
                        return [
                            'id' => $student->id,
                            'name' => $student->name,
                            'phone' => $student->phone_number,
                            'isAgency' => false,
                            'isOnline' => !is_null($student->location_id),
                            'profile_picture' => $student->profile_picture,
                            'location' => $student->location
                        ];
                    })) }},
                    ...{{ Js::from($agencies->map(function($agency) {
                        return [
                            'id' => $agency->id,
                            'name' => $agency->agency_name,
                            'phone' => $agency->contactPerson ? $agency->contactPerson->phone_number : $agency->agency_number,
                            'isAgency' => true,
                            'isOnline' => true,
                            'profile_picture' => $agency->agency_image,
                            'location' => $agency->location
                        ];
                    })) }}
                ];
            }
            
            // Apply search filter immediately after updating the items
            this.applySearchFilter();
        },
        
        applySearchFilter() {
            // This is handled automatically by the filteredList getter
        },
        
        initMap() {
            this.map = L.map('map').setView([{{ $latitude }}, {{ $longitude }}], 13);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_labels_under/{z}/{x}/{y}{r}.png', {
                attribution: '© <a href=\'https://www.openstreetmap.org/copyright\'>OpenStreetMap</a> contributors © <a href=\'https://carto.com/attributions\'>CARTO</a>',
                subdomains: 'abcd',
                maxZoom: 19
            }).addTo(this.map);

            // Add agencies
            const agencies = {{ Js::from($agencies) }};
            agencies.forEach(agency => {
                if (agency.location && agency.location.latitude && agency.location.longitude) {
                    const latLng = [agency.location.latitude, agency.location.longitude];
                    const marker = L.marker(latLng)
                        .addTo(this.map)
                        .bindPopup(`<b>${agency.agency_name}</b><br>${agency.location.location_name}`);
                    
                    L.circle(latLng, {
                        radius: parseFloat(agency.agency_radius) || 100,
                        color: '#3182ce',
                        fillColor: '#90cdf4',
                        fillOpacity: 0.3
                    }).addTo(this.map);
                    
                    this.agencyMarkers[agency.id] = marker;
                }
            });

            // Add students
            const students = {{ Js::from($mapStudents) }};
            students.forEach(user => {
                if (user.location && user.location.latitude && user.location.longitude) {
                    this.updateStudentMarker(user);
                }
            });
            
            // Refresh map size when switching between mobile/desktop views
            window.addEventListener('resize', () => {
                this.map.invalidateSize();
            });
        }
     }" 
     x-init="initMap(); updateFilteredItems();"
     class="h-full w-full bg-white p-2 md:p-6 relative">
    
    <!-- TOP BAR -->
    <div class="flex justify-between items-center py-2 px-4 bg-white shadow-md rounded-md mb-2">
        <h3 class="text-[10px] md:text-xs text-gray-600 mx-auto">March 20, 2025 - 12:45 PM</h3>
    </div>
    
    <!-- SEARCH + FILTER -->
    <div class="flex flex-col sm:flex-row gap-2 mb-4">
        <input x-model="searchQuery" type="text" placeholder="Search..." 
               @input="applySearchFilter()"
               class="w-full px-3 py-2 border rounded-md bg-transparent focus:outline-none text-xs">
        
        <!-- Modern Custom Dropdown -->
        <div class="relative w-full sm:w-auto">
            <button @click="isDropdownOpen = !isDropdownOpen" 
                    class="w-full px-3 py-2 border rounded-md bg-transparent focus:outline-none text-xs flex justify-between items-center">
                <span x-text="filter === 'all' ? 'All' : filter === 'student' ? 'Students' : 'Agencies'"></span>
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            
            <div x-show="isDropdownOpen" style="z-index: 500;" @click.away="isDropdownOpen = false" 
                 class="absolute mt-1 w-full bg-white rounded-md shadow-lg border border-gray-200">
                <ul class="py-1 text-xs">
                    <li>
                        <a href="#" @click.prevent="filter = 'all'; updateFilteredItems(); isDropdownOpen = false" 
                           class="block px-4 py-2 hover:bg-gray-100" :class="{'bg-emerald-50 text-emerald-600': filter === 'all'}">All</a>
                    </li>
                    <li>
                        <a href="#" @click.prevent="filter = 'student'; updateFilteredItems(); isDropdownOpen = false" 
                           class="block px-4 py-2 hover:bg-gray-100" :class="{'bg-emerald-50 text-emerald-600': filter === 'student'}">Students</a>
                    </li>
                    <li>
                        <a href="#" @click.prevent="filter = 'agency'; updateFilteredItems(); isDropdownOpen = false" 
                           class="block px-4 py-2 hover:bg-gray-100" :class="{'bg-emerald-50 text-emerald-600': filter === 'agency'}">Agencies</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="flex flex-col lg:flex-row gap-4">
        <!-- List -->
        <div class="w-full lg:w-1/4 border-none overflow-y-auto max-h-[300px] lg:max-h-[600px] order-2 lg:order-1">
            <template x-if="filteredList.length > 0">
                <template x-for="item in filteredList" :key="item.id">
                    <div @click="focusOnItem(item)" 
                         class="flex items-center p-3 bg-white shadow-md rounded-md mb-2 hover:bg-gray-50 cursor-pointer transition-colors">
                        <div class="relative">
                            <template x-if="item.profile_picture">
                                <img :src="item.profile_picture" class="w-8 h-8 md:w-10 md:h-10 rounded-full object-cover">
                            </template>
                            <template x-if="!item.profile_picture">
                                <div class="w-8 h-8 md:w-10 md:h-10 bg-gray-300 rounded-full flex items-center justify-center text-gray-500">
                                    <i class="fas fa-user text-sm"></i>
                                </div>
                            </template>
                            <div class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-white"
                                 :class="item.isOnline ? 'bg-green-500' : 'bg-gray-400'"></div>
                        </div>
                        
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-800 truncate" x-text="item.name"></p>
                            <p class="text-[10px] text-gray-500 truncate" x-text="item.phone"></p>
                            <p class="text-[10px] mt-1" :class="item.isAgency ? 'text-blue-600' : 'text-emerald-600'">
                                <span x-text="item.isAgency ? 'Agency' : 'Student'"></span>
                                <span x-show="!item.isAgency && !item.isOnline" class="text-gray-500 ml-1">(Offline)</span>
                            </p>
                        </div>
                        
                        <div class="ml-auto flex gap-2">
                            <a @click.stop :href="'tel:' + item.phone" 
                               class="text-gray-600 hover:text-blue-600 transition-colors"
                               :class="{'hover:text-emerald-600': !item.isAgency}">
                                <i class="fas fa-phone text-xs"></i>
                            </a>
                        </div>
                    </div>
                </template>
            </template>
            
            <template x-if="filteredList.length === 0">
                <div class="p-4 text-center text-gray-500 text-xs">
                    No items found matching your search criteria
                </div>
            </template>
        </div>

        <!-- MAP -->
        <div class="w-full lg:w-3/4 h-[300px] lg:h-[600px] bg-gray-200 relative rounded-lg overflow-hidden order-1 lg:order-2">
            <div id="map" class="absolute inset-0 w-full h-full" style="z-index: 100;"></div>
            
            <button class="absolute bottom-4 right-4 px-4 py-2 bg-blue-500 text-white rounded-md shadow-md hover:bg-blue-600 text-xs">
                <i class="fas fa-map-marker-alt"></i> Check-in
            </button>

            <div class="absolute bottom-4 left-4 flex items-center" style="z-index: 104;">
                <div class="flex items-center space-x-2 bg-white bg-opacity-80 px-3 py-1 rounded-md">
                    <span class="w-3 h-3 bg-emerald-500 rounded-full"></span>
                    <span class="text-xs text-gray-800">Student</span>
                    <span class="w-3 h-3 bg-blue-500 rounded-full ml-2"></span>
                    <span class="text-xs text-gray-800">Agency</span>
                </div>
            </div>
        </div>
    </div>

    <!-- STUDENT COMPLAINTS SECTION -->
    <div class="mt-6 bg-white shadow-md p-4 rounded-md">
        <h3 class="text-xs font-semibold text-gray-800">Student Complaints</h3>
        <div class="mt-2">
            <template x-for="complaint in [
                { id: 1, name: 'John Doe', message: 'Company supervisor is absent frequently.' },
                { id: 2, name: 'Jane Smith', message: 'Tasks assigned are unrelated to my field.' }
            ]" :key="complaint.id">
                <div class="p-3 bg-gray-100 rounded-md mb-2">
                    <p class="text-gray-800 font-semibold text-xs" x-text="complaint.name"></p>
                    <p class="text-gray-600 text-[10px]" x-text="complaint.message"></p>
                </div>
            </template>
        </div>
    </div>
</div>