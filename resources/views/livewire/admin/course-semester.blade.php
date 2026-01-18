<div class="p-3 md:p-6 w-full" x-data="{ 
    activeTab: 'semester', 
    search: '', 
    showPanel: false, 
    filterVisible: true,
    filterStatus: 'all',
    showModal: false,
    modalTitle: '',
    modalMessage: '',
    modalType: 'warning',
    modalIcon: '',
    isMobile: window.innerWidth < 768,
    bulkUpdateEnabled: false
}" x-init="
    window.addEventListener('resize', () => {
        isMobile = window.innerWidth < 768;
    });
">
    
    <!-- Breadcrumb Navigation -->
    <nav class="text-gray-600 mb-4">
        <span class="text-gray-500">Records</span> > 
        <span x-text="activeTab === 'semester' ? 'Semester' : 'Course'"></span>
    </nav>

    <!-- Mobile Tabs - Only visible on small screens -->
    <div class="block md:hidden mb-4">
        <div class="grid grid-cols-2 gap-2 bg-white shadow-sm rounded-lg overflow-hidden">
            <button 
                class="py-3 text-center transition-all duration-200 ease-in-out focus:outline-none"
                :class="activeTab === 'semester' ? 'bg-emerald-500 text-white' : 'bg-gray-50 text-gray-700 hover:bg-gray-100'"
                @click="activeTab = 'semester'; showPanel = false">
                <i class="fas fa-calendar-alt mr-2"></i>
                <span>Semester</span>
            </button>
            <button 
                class="py-3 text-center transition-all duration-200 ease-in-out focus:outline-none"
                :class="activeTab === 'course' ? 'bg-emerald-500 text-white' : 'bg-gray-50 text-gray-700 hover:bg-gray-100'"
                @click="activeTab = 'course'; showPanel = false">
                <i class="fas fa-graduation-cap mr-2"></i>
                <span>Course</span>
            </button>
        </div>
    </div>

    <div class="flex flex-col md:flex-row gap-4">
        
        <!-- Sidebar Filter - Hidden on mobile -->
<div 
    :class="filterVisible ? 'w-full md:w-1/3 lg:w-1/4' : 'w-0 overflow-hidden'" 
    class="bg-white shadow-md rounded-md p-4 md:p-6 md:h-screen transition-all duration-300 ease-in-out relative hidden sm:block">
    
    <!-- Chevron Button -->
    <button 
        @click="filterVisible = !filterVisible" 
        class="absolute top-4 md:top-6 right-4 md:right-6 text-gray-500 z-10">
        <i :class="filterVisible ? 'fas fa-chevron-left' : 'fas fa-chevron-right'"></i>
    </button>
    
    <!-- Sidebar Content -->
    <div x-show="filterVisible">
        <h2 class="text-lg font-semibold mb-4">Filters</h2>
        
        <!-- Desktop Sidebar Menu -->
        <ul class="space-y-3 hidden md:block">
            <li>
                <button 
                    class="w-full flex items-center gap-2 px-4 py-3 text-left rounded-md transition-all duration-200 ease-in-out focus:outline-none"
                    :class="activeTab === 'semester' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    @click="activeTab = 'semester'; showPanel = false">
                    <i class="fas fa-calendar-alt text-lg"></i>
                    <span class="flex-1">Semester/Grading Info</span>
                </button>
            </li>
            <li>
                <button 
                    class="w-full flex items-center gap-2 px-4 py-3 text-left rounded-md transition-all duration-200 ease-in-out focus:outline-none"
                    :class="activeTab === 'course' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    @click="activeTab = 'course'; showPanel = false">
                    <i class="fas fa-graduation-cap text-lg"></i>
                    <span class="flex-1">Course/Year Level</span>
                </button>
            </li>
        </ul>
    </div>
</div>
        
        <!-- Main Content -->
        <div class="flex-1 bg-white rounded-md p-4 md:p-6 transition-all duration-300" :class="showPanel && !isMobile ? 'w-2/3' : 'w-full'">
            <div class="flex flex-col md:flex-row">
                <!-- Left side content -->
                <div :class="showPanel && !isMobile ? 'w-full md:w-1/2 md:pr-4' : 'w-full'">
                    <template x-if="activeTab === 'semester'">
                        <div>
                            <h2 class="text-xl font-semibold">Semester/Grading Information</h2>
                            <p class="text-gray-500 text-sm mb-4">Manage and view semester grading details.</p>
                            
                            <!-- Search Bar -->
                            <div class="relative mb-4">
                                <input 
                                    type="text" 
                                    class="w-full pl-4 pr-10 py-3 rounded-full bg-gray-100 text-gray-700 focus:ring focus:ring-emerald-300 focus:outline-none" 
                                    placeholder="Search..." 
                                    x-model="search"
                                />
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                            </div>
                            
                            <!-- Filter Dropdown with Icon Next to It -->
                            <div class="flex justify-between items-center mb-4">
                                <div class="flex items-center gap-2">
                                    <label class="text-sm text-gray-700">Filter:</label>
                                    <div class="relative inline-block">
                                        <select x-model="filterStatus" class="appearance-none bg-white border border-gray-300 px-4 py-2 pr-8 rounded-md text-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-300">
                                            <option value="all">All</option>
                                            <option value="active">Active</option>
                                            <option value="completed">Completed</option>
                                            <option value="upcoming">Upcoming</option>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                            <i class="fas fa-chevron-down text-xs"></i>
                                        </div>
                                    </div>
                                    <i class="fas fa-filter text-gray-500 ml-2"></i>
                                </div>
                            </div>

                            <div class="space-y-4 border-t border-gray-200 pt-4">
                            @foreach ($semesters as $semester)
                                <div 
                                    x-show="(
                                        '{{ strtolower($semester->grading_code) }}'.includes(search.toLowerCase()) ||
                                        '{{ strtolower($semester->grading_description) }}'.includes(search.toLowerCase())
                                    ) && (
                                        filterStatus === 'all' ||
                                        filterStatus === '{{ strtolower($semester->status) }}'
                                    )"
                                    class="flex items-center justify-between bg-white shadow-sm p-4 rounded-md border border-gray-200 hover:bg-gray-50 transition"
                                >
                                    <div class="flex flex-col cursor-pointer" 
                                         @click="showPanel = true; if(isMobile) document.getElementById('detailPanel').scrollIntoView({behavior: 'smooth'});" 
                                         wire:click="selectSemester({{ $semester->id }})">
                                        <span class="text-lg font-semibold text-gray-800">{{ $semester->grading_code ?? 'N/A' }}</span>
                                        <span class="text-sm text-gray-500 truncate">{{ $semester->grading_description ?? 'No description available' }}</span>
                                    </div>
                        
                                    <div class="flex items-center space-x-2 md:space-x-4">
                                        @if ($semester->status === 'active')
                                          <span class="text-xs font-bold px-3 py-1 bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-full shadow-sm">
                                              Active
                                          </span>
                                          <button 
                                            @click="
                                                modalTitle = 'Mark as Completed';
                                                modalMessage = 'Are you sure you want to mark this semester as completed? This action cannot be undone.';
                                                modalType = 'warning';
                                                modalIcon = 'check-circle';
                                                showModal = true;
                                            "
                                            wire:click="markAsCompleted({{ $semester->id }})"
                                            class="text-sm text-red-600 hover:underline ml-2 hidden sm:block">
                                              Mark as Completed
                                          </button>
                                          <button 
                                            @click="
                                                modalTitle = 'Mark as Completed';
                                                modalMessage = 'Are you sure you want to mark this semester as completed? This action cannot be undone.';
                                                modalType = 'warning';
                                                modalIcon = 'check-circle';
                                                showModal = true;
                                            "
                                            wire:click="markAsCompleted({{ $semester->id }})"
                                            class="text-sm text-red-600 sm:hidden">
                                              <i class="fas fa-check-circle"></i>
                                          </button>
                                      
                                      @elseif ($semester->status === 'upcoming')
                                          <span class="text-xs font-medium px-3 py-1 bg-blue-100 text-blue-700 rounded-full">
                                              Upcoming
                                          </span>
                                          <button 
                                            @click="
                                                modalTitle = 'Set as Active';
                                                modalMessage = 'Are you sure you want to set this semester as active? Please ensure courses have been assigned to this semester.';
                                                modalType = 'warning';
                                                modalIcon = 'exclamation-triangle';
                                                showModal = true;
                                            "
                                            wire:click="setActiveSemester({{ $semester->id }})"
                                            class="text-sm text-blue-600 hover:underline ml-2 hidden sm:block">
                                              Set Active
                                          </button>
                                          <button 
                                            @click="
                                                modalTitle = 'Set as Active';
                                                modalMessage = 'Are you sure you want to set this semester as active? Please ensure courses have been assigned to this semester.';
                                                modalType = 'warning';
                                                modalIcon = 'exclamation-triangle';
                                                showModal = true;
                                            "
                                            wire:click="setActiveSemester({{ $semester->id }})"
                                            class="text-sm text-blue-600 sm:hidden">
                                              <i class="fas fa-play-circle"></i>
                                          </button>
                                      
                                      @elseif ($semester->status === 'completed')
                                          <span class="text-xs font-medium px-3 py-1 bg-gray-100 text-gray-700 rounded-full">
                                              Completed
                                          </span>
                                      @endif
                                      
                                      <!-- Delete Button -->
                                      @if ($semester->status === 'upcoming')
                                          <button 
                                              @click="
                                                modalTitle = 'Delete Semester';
                                                modalMessage = 'Are you sure you want to delete this semester? This action cannot be undone.';
                                                modalType = 'danger';
                                                modalIcon = 'trash-alt';
                                                showModal = true;
                                              "
                                              wire:click="deleteSemester({{ $semester->id }})"
                                              class="text-gray-400 hover:text-red-500 transition">
                                              <i class="fas fa-trash-alt"></i>
                                          </button>
                                      @endif
                                    </div>
                                </div>
                            @endforeach
                            </div>

                            <!-- Create Button -->
                            <div class="mt-6 flex justify-end">
                                <button 
                                    @click="showPanel = true; if(isMobile) setTimeout(() => document.getElementById('detailPanel').scrollIntoView({behavior: 'smooth'}), 100);" 
                                    wire:click="resetSemesterForm()" 
                                    class="bg-emerald-600 text-white px-5 py-2 rounded-md hover:bg-emerald-700 transition font-medium flex items-center gap-2">
                                    <i class="fas fa-plus"></i> Add Semester
                                </button>
                            </div>
                        </div>
                    </template>

                    <template x-if="activeTab === 'course'">
                        <div>
                            <h2 class="text-xl font-semibold">Course & Year Level</h2>
                            <p class="text-gray-500 text-sm mb-4">View and manage courses and year levels.</p>
                            
                            <!-- Search Bar -->
                            <div class="relative mb-4">
                                <input 
                                    type="text" 
                                    class="w-full pl-4 pr-10 py-3 rounded-full bg-gray-100 text-gray-700 focus:ring focus:ring-emerald-300 focus:outline-none" 
                                    placeholder="Search..."
                                    x-model="search"
                                />
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                            </div>
                            
                            
                            
                            <!-- Bulk Update Input (visible only for Course tab) -->
                            <div class="mb-4 p-4 border border-emerald-400 bg-emerald-50 rounded-md shadow-sm">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <h3 class="text-md font-semibold text-emerald-800">Bulk Update Hours</h3>
                                        <p class="text-sm text-emerald-600">Update <strong>all</strong> existing courses' total hours. Use with caution.</p>
                                    </div>
                                    <label class="inline-flex items-center cursor-pointer mt-2 sm:mt-0 sm:ml-4">
                                        <input type="checkbox" wire:model="bulkUpdateEnabled" x-model="bulkUpdateEnabled" class="form-checkbox h-5 w-5 text-emerald-500">
                                        <span class="ml-2 text-sm text-gray-700">Enable</span>
                                    </label>
                                </div>
                                  
                                <div class="mt-4 flex items-center gap-2" x-show="bulkUpdateEnabled">
                                    <input 
                                        type="number" 
                                        step="0.1"
                                        min="0"
                                        wire:model.defer="bulkTotalHours"
                                        class="flex-1 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-emerald-400 focus:outline-none"
                                        placeholder="Enter hours (e.g. 3.5)"
                                    >
                                    <button 
                                        @click="
                                            modalTitle = 'Bulk Update Hours';
                                            modalMessage = 'Are you sure you want to update hours for ALL courses? This action will affect all existing courses.';
                                            modalType = 'warning';
                                            modalIcon = 'exclamation-triangle';
                                            showModal = true;
                                        "
                                        wire:click="updateAllCoursesHours"
                                        class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-md shadow font-semibold transition">
                                        Update
                                    </button>
                                </div>
                            </div>
                          
                            <!-- Course Table -->
                            <div class="space-y-3">
                              @foreach ($courses as $course)
                                <div 
                                  x-show="
                                      '{{ strtolower($course->abbreviation) }}'.includes(search.toLowerCase()) ||
                                      '{{ strtolower($course->full_name) }}'.includes(search.toLowerCase())"
                                  class="flex items-center justify-between bg-white shadow-sm p-3 rounded-md border border-gray-200 hover:bg-gray-50 transition"
                                  >
                                  <div 
                                      @click="showPanel = true; if(isMobile) setTimeout(() => document.getElementById('detailPanel').scrollIntoView({behavior: 'smooth'}), 100);" 
                                      wire:click="selectCourse({{ $course->id }})"
                                      class="flex items-center cursor-pointer flex-1"
                                  >
                                      <div class="w-2 h-6 bg-green-500 rounded-md"></div>
                                      <span class="ml-3 font-semibold text-gray-700">{{ $course->abbreviation }}</span>
                                      <span class="ml-4 text-gray-600 truncate hidden sm:block">{{ $course->full_name }}</span>
                                  </div>
                                  <button 
                                    @click="
                                        modalTitle = 'Delete Course';
                                        modalMessage = 'Are you sure you want to delete this course? This action cannot be undone.';
                                        modalType = 'danger';
                                        modalIcon = 'trash-alt';
                                        showModal = true;
                                    "
                                    wire:click="deleteCourse({{ $course->id }})" 
                                    class="text-gray-400 hover:text-red-500">
                                      <i class="fas fa-trash"></i>
                                  </button>
                                </div>
                              @endforeach
                            </div>
                            
                            <!-- Create Button -->
                            <div class="mt-6 flex justify-end">
                                <button 
                                    @click="showPanel = true; if(isMobile) setTimeout(() => document.getElementById('detailPanel').scrollIntoView({behavior: 'smooth'}), 100);" 
                                    wire:click="resetCourseForm()" 
                                    class="bg-emerald-600 text-white px-5 py-2 rounded-md hover:bg-emerald-700 transition font-medium flex items-center gap-2">
                                    <i class="fas fa-plus"></i> Add Course
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
                
                <!-- Right Panel - Now inline and responsive -->
                <div 
                    id="detailPanel"
                    x-show="showPanel" 
                    x-transition:enter="transition transform ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 md:translate-y-0 md:translate-x-4"
                    x-transition:enter-end="opacity-100 translate-y-0 md:translate-x-0"
                    x-transition:leave="transition transform ease-in duration-300"
                    x-transition:leave-start="opacity-100 translate-y-0 md:translate-x-0"
                    x-transition:leave-end="opacity-0 translate-y-4 md:translate-y-0 md:translate-x-4"
                    class="w-full md:w-1/2 bg-white mt-6 md:mt-0 p-4 md:p-6 md:border-l md:border-gray-200 rounded-md md:rounded-none shadow-md md:shadow-none overflow-y-auto">
                    
                    <!-- Header with Modern Design -->
                    <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                            <i class="fas fa-pencil-alt mr-2 text-emerald-500"></i>
                            <span x-text="activeTab === 'course' ? 'Course Details' : 'Semester Details'"></span>
                        </h2>
                        
                        <!-- Close Button -->
                        <button @click="showPanel = false" class="text-gray-400 hover:text-gray-800 transition rounded-full hover:bg-gray-100 p-2">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                
                    <!-- COURSE FORM -->
                    <template x-if="activeTab === 'course'">
                        <div class="space-y-4">
                            <div class="form-group">
                                <label class="block text-gray-700 font-medium mb-1">Abbreviation</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-tag text-gray-400"></i>
                                    </div>
                                    <input type="text" class="w-full border pl-10 px-4 py-2 rounded-md focus:ring-2 focus:ring-emerald-300 focus:border-emerald-300 outline-none transition" wire:model="newCourseAbbreviation">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="block text-gray-700 font-medium mb-1">Full Name</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-book text-gray-400"></i>
                                    </div>
                                    <input type="text" class="w-full border pl-10 px-4 py-2 rounded-md focus:ring-2 focus:ring-emerald-300 focus:border-emerald-300 outline-none transition" wire:model="newCourseFullName">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="block text-gray-700 font-medium mb-1">Assign Year Levels</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-layer-group text-gray-400"></i>
                                    </div>
                                    <select multiple wire:model="selectedYearLevels" class="w-full border pl-10 px-4 py-2 rounded-md focus:ring-2 focus:ring-emerald-300 focus:border-emerald-300 outline-none transition">
                                        @foreach ($yearLevels as $yearLevel)
                                            <option value="{{ $yearLevel->id }}">{{ $yearLevel->level ?? 'No Year Levels' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="block text-gray-700 font-medium mb-1">Total Hours</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-clock text-gray-400"></i>
                                    </div>
                                    <input 
                                        id="formattedTotalHours" 
                                        type="text" 
                                        class="w-full border pl-10 px-4 py-2 rounded-md focus:ring-2 focus:ring-emerald-300 focus:border-emerald-300 outline-none transition" 
                                        wire:model.lazy="formattedTotalHours"
                                        placeholder="e.g. 300:00:00"
                                    />
                                </div>
                            </div>
                            
                            <div class="mt-8 flex flex-wrap gap-3 justify-end">
                                <button @click="showPanel = false" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-100 transition">
                                    Cancel
                                </button>
                                @if ($selectedCourse)
                                    <button @click="showPanel = false" wire:click="updateCourse()" class="bg-emerald-500 text-white px-6 py-2 rounded-md hover:bg-emerald-600 transition flex items-center">
                                        <i class="fas fa-save mr-2"></i> Update
                                    </button>
                                @else
                                    <button @click="showPanel = false" wire:click="addCourse()" class="bg-emerald-500 text-white px-6 py-2 rounded-md hover:bg-emerald-600 transition flex items-center">
                                        <i class="fas fa-plus mr-2"></i> Create
                                    </button>
                                @endif
                            </div>
                        </div>
                    </template>
                
                    <!-- SEMESTER FORM -->
                    <template x-if="activeTab === 'semester'">
                        <div class="space-y-4">
                            <div class="form-group">
                                <label class="block text-gray-700 font-medium mb-1">Grading Code</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-code text-gray-400"></i>
                                    </div>
                                    <input type="text" class="w-full border pl-10 px-4 py-2 rounded-md focus:ring-2 focus:ring-emerald-300 focus:border-emerald-300 outline-none transition" wire:model="newGradingCode">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="block text-gray-700 font-medium mb-1">Grading Description</label>
                                <div class="relative">
                                    <div class="absolute top-3 left-0 pl-3 flex items-start pointer-events-none">
                                        <i class="fas fa-info-circle text-gray-400"></i>
                                    </div>
                                    <textarea class="w-full border pl-10 px-4 py-2 rounded-md focus:ring-2 focus:ring-emerald-300 focus:border-emerald-300 outline-none transition h-24" wire:model="newGradingDescription"></textarea>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="block text-gray-700 font-medium mb-1">Assign Courses</label>
                                <div class="border rounded-md p-3 bg-gray-50 max-h-40 overflow-y-auto">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        @foreach ($courses as $course)
                                            <label class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded transition">
                                                <input type="checkbox" wire:model="selectedCourses" value="{{ $course->id }}" class="rounded text-emerald-500 focus:ring-emerald-300">
                                                <span>{{ $course->abbreviation }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-8 flex flex-wrap gap-3 justify-end">
                                <button @click="showPanel = false" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-100 transition">
                                    Cancel
                                </button>
                                @if ($selectedSemester)
                                    <button @click="showPanel = false" wire:click="updateSemester()" class="bg-emerald-500 text-white px-6 py-2 rounded-md hover:bg-emerald-600 transition flex items-center">
                                        <i class="fas fa-save mr-2"></i> Update
                                    </button>
                                @else
                                    <button @click="showPanel = false" wire:click="addSemester()" class="bg-emerald-500 text-white px-6 py-2 rounded-md hover:bg-emerald-600 transition flex items-center">
                                        <i class="fas fa-plus mr-2"></i> Create
                                    </button>
                                @endif
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Warning Modal -->
    <div 
        x-cloak
        x-show="showModal" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center">
        <!-- Modal content remains the same -->
        
        <!-- Modal Backdrop -->
        <div class="absolute inset-0 bg-black bg-opacity-50" @click="showModal = false"></div>
        
        <!-- Modal Content -->
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full z-10 overflow-hidden transform transition-all">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i :class="'fas fa-' + modalIcon" class="mr-2 text-amber-500 text-xl"></i>
                        <span x-text="modalTitle"></span>
                    </h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="mt-3">
                    <p class="text-sm text-gray-500" x-text="modalMessage"></p>
                </div>
                
                <div class="mt-5 flex justify-end space-x-3">
                    <button 
                        @click="showModal = false" 
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition">
                        Cancel
                    </button>
                    <button 
                        @click="showModal = false"
                        class="px-4 py-2 bg-emerald-500 text-white rounded-md hover:bg-emerald-600 transition">
                        Proceed
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <style>
      [x-cloak] { display: none !important; }
    </style>
</div>