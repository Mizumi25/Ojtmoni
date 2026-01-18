<div x-data="{
    open: false,
    activePage: window.location.pathname,
    textMap: {},
    isAdmin: @json(auth()->user()->role === 'admin'),
    isCoordinator: @json(auth()->user()->role === 'coordinator'),
    isAgency: @json(auth()->user()->role === 'agency'),
    isStudent: @json(auth()->user()->role === 'student'),
    isStudentApproved: @json(auth()->user()->status === 'approved'),
    isStudentIntern: @json(auth()->user()->status === 'intern'),
    openDropdown: false,

    expandText() {
        document.querySelectorAll('.sidebar-link').forEach(link => {
            let text = link.getAttribute('data-text');
            if (text) { // Add this check
                this.textMap[text] = '';
                [...text].forEach((char, index) => {
                    setTimeout(() => this.textMap[text] += char, index * 50);
                });
            }
        });
    },

    collapseText() {
        document.querySelectorAll('.sidebar-link').forEach(link => {
            let text = link.getAttribute('data-text');
            this.textMap[text] = '';
        });
    }
}" 
x-init="document.querySelectorAll('.sidebar-link').forEach(link => { 
    let text = link.getAttribute('data-text'); 
    textMap[text] = ''; 
})" 
class="flex z-50">

    <!-- Sidebar -->
    <aside class="@if (auth()->user()->role === 'student')
                  fixed
                  bottom-4 left-1/2 -translate-x-1/2 transition-transform duration-300 transform -translate-x-1/2 bg-black opacity-40 text-white w-80 rounded-full py-4 flex justify-around items-center shadow-lg px-5
              @else 
                  bg-white h-screen px-3 text-gray flex flex-col relative py-4 transition-all duration-300
              @endif"
           :class="open ? 'w-60' : 'w-20'">
        
        <!-- Sidebar Toggle Button -->
        @if (auth()->user()->role !== 'student')
            <div class="relative w-full hidden md:flex items-center transition-all duration-300" :class="open ? 'justify-between' : 'justify-center'">
                <span class="py-2 pr-2 flex items-center font-bold text-gray-900 uppercase transition-all duration-300 cursor-default">
                    <img src="{{ asset('images/logo.png') }}" alt="GCC Logo" class="inline-block w-10 h-10 rounded-full">
                    <h1 x-show="open" class="ml-2 text-sm" x-text="textMap['OJT Monitor']"></h1>
                </span>
                <button @click="open = !open; 
                                if (open) { 
                                    expandText(); 
                                    let titleText = 'OJT Monitor'; 
                                    textMap[titleText] = ''; 
                                    [...titleText].forEach((char, index) => { 
                                        setTimeout(() => textMap[titleText] += char, index * 50); 
                                    }); 
                                } else { 
                                    collapseText(); 
                                    textMap['OJT Monitor'] = ''; 
                                }"
                        :class="open ? 'right-0' : 'right-[-30px]'"
                        class="absolute bottom-[-30px] -translate-y-1/2 bg-gray-50 border rounded-sm w-6 h-6 flex items-center justify-center shadow-md focus:outline-none transition-all duration-300 cursor-pointer z-50">
                    <i :class="open ? 'fa-solid fa-angles-left text-gray-400 text-[0.7rem]' : 'fa-solid fa-angles-right text-gray-400 text-[0.7rem]'"
                       class="transition-all duration-300"></i>
                </button>
            </div>
        @endif
                  
        <!-- Coordinator Navigation (Only for Coordinator Role) -->
        <template x-if="isCoordinator">
            <nav class="mt-4">
                <!-- Dashboard -->
                <a href="/" wire:navigate class="sidebar-link p-2 flex items-center rounded transition-all duration-300 text-gray-500"
                   :class="activePage === '/' ? 'bg-emerald-600 rounded-1xl text-white' : 'hover:text-emerald-600'"
                   data-text="Dashboard"
                   @click="activePage = '/'">
                    <i :class="activePage === '/' ? 'text-white' : 'text-gray-500'" class="fas fa-home text-lg"></i>
                    <span x-show="open" x-text="textMap['Dashboard']" class="ml-3"></span>
                </a>
                
                <hr class="w-full border-t-2 border-gray-300 rounded-full my-4">

                <!-- User Management -->
                <a href="/users" wire:navigate class="sidebar-link p-2 flex items-center rounded transition-all duration-300 text-gray-500"
                   :class="activePage === '/users' ? 'bg-emerald-600 rounded-1xl text-white' : 'hover:text-emerald-600'"
                   data-text="Students"
                   @click="activePage = '/users'">
                    <i :class="activePage === '/users' ? 'text-white' : 'text-gray-500'" class="fas fa-user text-lg"></i>
                    <span x-show="open" x-text="textMap['Students']" class="ml-3"></span>
                </a>

                <!-- Progress Report -->
                <a href="/progress" wire:navigate class="sidebar-link p-2 flex items-center rounded transition-all duration-300 text-gray-500"
                   :class="activePage === '/progress' ? 'bg-emerald-600 rounded-1xl text-white' : 'hover:text-emerald-600'"
                   data-text="Progress Report"
                   @click="activePage = '/progress'">
                    <i :class="activePage === '/progress' ? 'text-white' : 'text-gray-500'" class="fas fa-chart-line text-lg"></i>
                    <span x-show="open" x-text="textMap['Progress Report']" class="ml-3"></span>
                </a>
                
                <!-- Company Management -->
                <a href="/companies" wire:navigate class="sidebar-link p-2 flex items-center rounded transition-all duration-300 text-gray-500"
                   :class="activePage === '/companies' ? 'bg-emerald-600 rounded-1xl text-white' : 'hover:text-emerald-600'"
                   data-text="Company"
                   @click="activePage = '/companies'">
                    <i :class="activePage === '/companies' ? 'text-white' : 'text-gray-500'" class="fas fa-building text-lg"></i>
                    <span x-show="open" x-text="textMap['Company']" class="ml-3"></span>
                </a>
                
                <!-- Assignment Management -->
                <a href="/assignments" wire:navigate class="sidebar-link p-2 flex items-center rounded transition-all duration-300 text-gray-500"
                   :class="activePage === '/assignments' ? 'bg-emerald-600 rounded-1xl text-white' : 'hover:text-emerald-600'"
                   data-text="Assignment"
                   @click="activePage = '/assignments'">
                    <i :class="activePage === '/assignments' ? 'text-white' : 'text-gray-500'" class="fas fa-tasks text-lg"></i>
                    <span x-show="open" x-text="textMap['Assignment']" class="ml-3"></span>
                </a>
                
                <!-- Schedule -->
                <a href="/schedules" wire:navigate class="sidebar-link p-2 flex items-center rounded transition-all duration-300 text-gray-500"
                   :class="activePage === '/schedules' ? 'bg-emerald-600 rounded-1xl text-white' : 'hover:text-emerald-600'"
                   data-text="Schedule"
                   @click="activePage = '/schedules'">
                    <i :class="activePage === '/schedules' ? 'text-white' : 'text-gray-500'" class="fas fa-clock text-lg"></i>
                    <span x-show="open" x-text="textMap['Schedule']" class="ml-3"></span>
                </a>
                
                
                <hr class="w-full border-t-2 border-gray-300 rounded-full my-4">

                <!-- Tracking Map -->
                <a href="/tracks" wire:navigate class="sidebar-link p-2 flex items-center rounded transition-all duration-300 text-gray-500"
                   :class="activePage === '/tracks' ? 'bg-emerald-600 rounded-1xl text-white' : 'hover:text-emerald-600'"
                   data-text="View Map"
                   @click="activePage = '/tracks'">
                    <i :class="activePage === '/tracks' ? 'text-white' : 'text-gray-500'" class="fas fa-map-marked-alt text-lg"></i>
                    <span x-show="open" x-text="textMap['View Map']" class="ml-3"></span>
                </a>

            </nav>
        </template>

        <!-- Admin Navigation (Only for Admin Role) -->
            <template x-if="isAdmin">
            <div class="mt-4">
                <div x-data="{ openDropdown: false }" >
                     <!-- Dashboard -->
                    <a href="/dashboard" class="sidebar-link px-2.5 py-3 flex items-center rounded transition-all duration-300 text-gray-500"
                       :class="activePage === '/dashboard' ? 'bg-emerald-600 rounded-1xl text-white' : 'hover:text-emerald-600'"
                       data-text="Dashboard"
                       @click="activePage = '/dashboard'">
                        <i :class="activePage === '/dashboard' ? 'text-white' : 'text-gray-500'" class="fas fa-home text-lg"></i>
                        <span x-show="open" x-text="textMap['Dashboard']" class="ml-3"></span>
                    </a>
                    
                    <hr class="w-full border-t-2 border-gray-300 rounded-full my-4">
                    
                    <a href="/course-sem" class="sidebar-link px-2.5 py-3 flex items-center rounded transition-all duration-300 text-gray-500"
                       :class="activePage === '/course-sem' ? 'bg-emerald-600 rounded-1xl text-white' : 'hover:text-emerald-600'"
                       data-text="Course & Semester"
                       @click="activePage = '/course-sem'">
                        <i :class="activePage === '/course-sem' ? 'text-white' : 'text-gray-500'" class="fas fa-book text-lg"></i>
                        <span x-show="open" x-text="textMap['Course & Semester']" class="ml-3"></span>
                    </a>
        
                    
                  <!-- Dropdown Button -->
                  <div class="relative">
                    <button @click="openDropdown = !openDropdown"
                          class="w-full px-2.5 py-3 flex items-center justify-between rounded transition-all duration-300 hover:text-emerald-600 text-gray-500"
                          :class="!open ? 'justify-center' : ''">
                      <span class="flex items-center">
                          <i class="fas fa-folder text-lg"></i>
                          <span x-show="open" class="ml-2 text-gray-500">Management</span>
                      </span>
                      <i class="text-sm" :class="openDropdown ? 'fas fa-chevron-down' : 'fas fa-chevron-right'" x-show="open"></i>
                  </button>
              
                  <!-- Dropdown Items -->
                  <div x-show="openDropdown"
                      x-transition
                      class="bg-white rounded-md py-2 z-50"
                      :class="open ? 'relative ml-4 space-y-1' : 'absolute left-14 top-0 w-44' + (open ? '' : ' shadow-lg')">
                     <a href="/coordinator-management" class="sidebar-link flex items-center px-3 py-2 rounded transition-all duration-300 text-gray-500 hover:text-emerald-600"
                       :class="activePage === '/coordinator-management' ? 'bg-emerald-600 rounded-1xl text-white' : ''">
                        <i class="fas fa-user-tie text-lg" :class="activePage === '/coordinator-management' ? 'text-white' : 'text-gray-500'" ></i>
                        <span class="ml-2">Coordinator</span>
                      </a>
                      <a href="/student-management" class="sidebar-link flex items-center px-3 py-2 rounded transition-all duration-300 text-gray-500 hover:text-emerald-600"
                         :class="activePage === '/student-management' ? 'bg-emerald-600 rounded-1xl text-white' : ''">
                          <i class="fas fa-user-graduate text-lg" :class="activePage === '/student-management' ? 'text-white' : 'text-gray-500'" ></i>
                          <span class="ml-2">Student</span>
                      </a>
                      <a href="/company-management" class="sidebar-link flex items-center px-3 py-2 rounded transition-all duration-300 text-gray-500 hover:text-emerald-600"
                         :class="activePage === '/company-management' ? 'bg-emerald-600 rounded-1xl text-white' : ''">
                          <i class="fas fa-building text-lg" :class="activePage === '/company-management' ? 'text-white' : 'text-gray-500'" ></i>
                          <span class="ml-2">Company</span>
                      </a>
                  </div>
                  </div>
                    <!-- Tracking Map -->
                    <a href="/tracks" wire:navigate class="sidebar-link p-2 flex items-center rounded transition-all duration-300 text-gray-500"
                       :class="activePage === '/tracks' ? 'bg-emerald-600 rounded-1xl text-white' : 'hover:text-emerald-600'"
                       data-text="View Map"
                       @click="activePage = '/tracks'">
                        <i :class="activePage === '/tracks' ? 'text-white' : 'text-gray-500'" class="fas fa-map-marked-alt text-lg"></i>
                        <span x-text="textMap['View Map']" class="ml-3"></span>
                    </a>
                    
                     <!-- Schedule Management -->
                    <a href="/schedules" wire:navigate class="sidebar-link p-2 flex items-center rounded transition-all duration-300 text-gray-500"
                       :class="activePage === '/schedules' ? 'bg-emerald-600 rounded-1xl text-white' : 'hover:text-emerald-600'"
                       data-text="Schedule"
                       @click="activePage = '/schedules'">
                        <i :class="activePage === '/schedules' ? 'text-white' : 'text-gray-500'" class="fas fa-clock text-lg"></i>
                        <span x-text="textMap['Schedule']" class="ml-3"></span>
                    </a>
                </div>
            </div>
        </template>
        
        
        <!-- Agency Navigation (Only for Agency Role) -->
        <template x-if="isAgency">
            <nav class="flex flex-col mt-6 w-full space-y-2">
                <!-- Dashboard -->
                <a href="/dashboards" wire:navigate class="sidebar-link p-2 flex items-center rounded transition-all duration-300 text-gray-500"
                   :class="activePage === '/dashboards' ? 'bg-emerald-600 rounded-1xl text-white' : 'hover:text-emerald-600'"
                   data-text="Dashboard"
                   @click="activePage = '/dashboards'">
                    <i :class="activePage === '/dashboards' ? 'text-white' : 'text-gray-500'" class="fas fa-home text-lg"></i>
                    <span x-show="open" x-text="textMap['Dashboard']" class="ml-3"></span>
                </a>
                
                <!-- View Semester -->
                <a href="/semester" wire:navigate class="sidebar-link p-2 flex items-center rounded transition-all duration-300 text-gray-500"
                   :class="activePage === '/semester' ? 'bg-emerald-600 rounded-1xl text-white' : 'hover:text-emerald-600'"
                   data-text="View Semester"
                   @click="activePage = '/semester'">
                    <i :class="activePage === '/semester' ? 'text-white' : 'text-gray-500'" class="fas fa-calendar-alt text-lg"></i>
                    <span x-show="open" x-text="textMap['View Semester']" class="ml-3"></span>
                </a>
                
                <!-- Student List -->
                <a href="/listofstudents" wire:navigate class="sidebar-link p-2 flex items-center rounded transition-all duration-300 text-gray-500"
                   :class="activePage === '/listofstudents' ? 'bg-emerald-600 rounded-1xl text-white' : 'hover:text-emerald-600'"
                   data-text="Student List"
                   @click="activePage = '/listofstudents'">
                    <i :class="activePage === '/listofstudents' ? 'text-white' : 'text-gray-500'" class="fas fa-user-alt text-lg"></i>
                    <span x-show="open" x-text="textMap['Student List']" class="ml-3"></span>
                </a>
                
                <hr class="w-full border-t-2 border-gray-300 rounded-full my-4">
                
                <a href="/schedules" wire:navigate class="sidebar-link p-2 flex items-center rounded transition-all duration-300 text-gray-500"
                   :class="activePage === '/schedules' ? 'bg-emerald-600 rounded-1xl text-white' : 'hover:text-emerald-600'"
                   data-text="Schedule"
                   @click="activePage = '/schedules'">
                    <i :class="activePage === '/schedules' ? 'text-white' : 'text-gray-500'" class="fas fa-clock text-lg"></i>
                    <span x-show="open" x-text="textMap['Schedule']" class="ml-3"></span>
                </a>
            </nav>
        </template>
        
        
        
        <template x-if="isStudent">
          <nav class="w-full flex gap-x-2 justify-between items-center">
              <!-- Dashboard -->
              <template x-if="isStudentIntern">
                  <a href="/dashboardss" wire:navigate class="sidebar-link flex items-center rounded p-5 rounded-full transition-all duration-300"
                     :class="activePage === '/dashboardss' ? 'bg-emerald-600 text-white' : 'bg-gray-500 opacity-80 hover:text-emerald-600'"
                     @click="activePage = '/dashboardss'">
                      <i :class="activePage === '/dashboardss' ? 'text-gray-100' : 'text-white'" class="fas fa-home text-lg z-10"></i>
                  </a>
              </template>
      
              <!-- Attendance Portal -->
              <template x-if="isStudentIntern">
                  <a href="/attendance" wire:navigate class="sidebar-link flex items-center rounded p-5 rounded-full transition-all duration-300"
                     :class="activePage === '/attendance' ? 'bg-emerald-600 text-white' : 'bg-gray-500 opacity-80 hover:text-emerald-600'"
                     @click="activePage = '/attendance'">
                      <i :class="activePage === '/attendance' ? 'text-gray-100' : 'text-white'" class="fas fa-clipboard-list text-lg z-10"></i>
                  </a>
              </template>
      
              <!-- Agency Reports -->
              <template x-if="isStudentIntern">
                  <a href="/agency-reports" wire:navigate class="sidebar-link flex items-center rounded p-5 rounded-full transition-all duration-300"
                     :class="activePage === '/agency-reports' ? 'bg-emerald-600 text-white' : 'bg-gray-500 opacity-80 hover:text-emerald-600'"
                     @click="activePage = '/agency-reports'">
                      <i :class="activePage === '/agency-reports' ? 'text-gray-100' : 'text-white'" class="fas fa-file-alt text-lg z-10"></i>
                  </a>
              </template>
      
              <!-- Agency Application -->
              <template x-if="isStudentApproved">
                  <a href="{{ route('application.agency') }}" wire:navigate class="sidebar-link flex items-center rounded p-5 rounded-full transition-all duration-300"
                     :class="activePage === '{{ route('application.agency') }}' ? 'bg-emerald-600 text-white' : 'bg-gray-500 opacity-80 hover:text-emerald-600'"
                     @click="activePage = '{{ route('application.agency') }}'">
                      <i :class="activePage === '{{ route('application.agency') }}' ? 'text-gray-100' : 'text-white'" class="fas fa-edit text-lg z-10"></i>
                  </a>
              </template>
      
              <!-- Orientation Resources -->
              <template x-if="isStudentApproved">
                  <a href="{{ route('orientation.resources') }}" wire:navigate class="sidebar-link flex items-center rounded p-5 rounded-full transition-all duration-300"
                     :class="activePage === '{{ route('orientation.resources') }}' ? 'bg-emerald-600 text-white' : 'bg-gray-500 opacity-80 hover:text-emerald-600'"
                     @click="activePage = '{{ route('orientation.resources') }}'">
                      <i :class="activePage === '{{ route('orientation.resources') }}' ? 'text-gray-100' : 'text-white'" class="fas fa-book text-lg z-10"></i>
                  </a>
              </template>
      
              <!-- Message -->
              <a href="/message" wire:navigate class="sidebar-link flex items-center rounded p-5 rounded-full transition-all duration-300"
                 :class="activePage === '/message' ? 'bg-emerald-600 text-white' : 'bg-gray-500 opacity-80 hover:text-emerald-600'"
                 @click="activePage = '/message'">
                  <i :class="activePage === '/message' ? 'text-gray-100' : 'text-white'" class="fas fa-envelope text-lg z-10"></i>
              </a>
          </nav>
      </template>

    </aside>

   
</div>