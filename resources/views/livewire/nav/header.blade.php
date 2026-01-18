<header class="w-full {{ auth()->user() && auth()->user()->role === 'student' ? 'bg-transparent fixed h-40' : 'bg-white' }} py-2 px-4 z-[60]" 
        x-data="{ 
            notificationOpen: false,
            profileOpen: false,
            mobileMenuOpen: false,
            searchOpen: false,
            searchQuery: '',
            searchResults: [],
            isLoading: false,
            searchRoles: {
                'admin': ['Students', 'Coordinators', 'Companies', 'Courses'],
                'coordinator': ['Students', 'Companies', 'Assignments'],
                'agency': ['Students', 'Reports'],
                'student': ['Agencies', 'Resources']
            },
            async search() {
                if (this.searchQuery.length < 2) {
                    this.searchResults = [];
                    return;
                }
                
                this.isLoading = true;
                
                // Simulate API call with timeout
                await new Promise(resolve => setTimeout(resolve, 500));
                
                // Generate mock results based on role
                const role = '{{ auth()->user()->role ?? '' }}';
                this.searchResults = this.searchRoles[role] 
                    ? this.searchRoles[role].map(item => ({
                        title: `${item} Search Result`,
                        description: `Showing ${item.toLowerCase()} matching '${this.searchQuery}'`,
                        url: this.getSearchUrl(role, item)
                    }))
                    : [];
                
                this.isLoading = false;
                this.searchOpen = true;
            },
            getSearchUrl(role, type) {
                const routes = {
                    'admin': {
                        'Students': '/student-management',
                        'Coordinators': '/coordinator-management',
                        'Companies': '/company-management',
                        'Courses': '/course-sem'
                    },
                    'coordinator': {
                        'Students': '/users',
                        'Companies': '/companies',
                        'Assignments': '/assignments'
                    },
                    'agency': {
                        'Students': '/listofstudents',
                        'Reports': '/dashboards'
                    },
                    'student': {
                        'Agencies': '/application',
                        'Resources': '/resources'
                    }
                };
                return routes[role]?.[type] || '#';
            }
        }" 
        x-cloak>
    @if (Route::has('login'))
        <nav class="flex w-full items-center pl-4 pr-4">
            <div class="flex-grow">
                @auth
                    @if(auth()->user() && auth()->user()->role !== 'student' && auth()->user()->role !== 'agency')
                        <div class="relative w-[50%] max-w-md">
                            <input 
                                type="text" 
                                placeholder="Search..."
                                x-model="searchQuery"
                                @input="search()"
                                @keyup="search()"
                                @focus="if(searchQuery.length >= 2) searchOpen = true"
                                class="w-full pl-10 pr-3 py-1.5 rounded-[60px] bg-white text-gray-600 border-2 border-gray-300 placeholder-gray-500 focus:ring-2 focus:ring-green-700">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500"></i>
                            
                            <!-- Search Results Dropdown -->
                            <div x-show="searchOpen && searchResults.length > 0"
                                 @click.away="searchOpen = false"
                                 x-transition
                                 class="absolute z-50 mt-2 w-full bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
                                <template x-for="(result, index) in searchResults" :key="index">
                                    <a :href="result.url" 
                                       class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0"
                                       @click.prevent="window.location.href = result.url">
                                        <div class="font-medium text-gray-800" x-text="result.title"></div>
                                        <div class="text-sm text-gray-500" x-text="result.description"></div>
                                    </a>
                                </template>
                            </div>
                            
                            <!-- Loading State -->
                            <div x-show="isLoading" 
                                 class="absolute z-50 mt-2 w-full bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200 p-4">
                                <div class="flex items-center justify-center">
                                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-green-600"></div>
                                    <span class="ml-2 text-gray-600">Searching...</span>
                                </div>
                            </div>
                            
                            <!-- No Results -->
                            <div x-show="searchOpen && !isLoading && searchResults.length === 0 && searchQuery.length >= 2"
                                 class="absolute z-50 mt-2 w-full bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200 p-4 text-gray-500">
                                No results found for "<span x-text="searchQuery"></span>"
                            </div>
                        </div>
                    @endif
                @endauth
            </div>

            <!-- Rest of your header code remains exactly the same -->
            <div class="flex items-center gap-6">
                @auth
                    @if(auth()->user() && auth()->user()->role === 'student')
                        <div class="flex items-center">
                            <button @click="mobileMenuOpen = !mobileMenuOpen" class="flex flex-col justify-center items-center space-y-1 p-3 rounded-full">
                                <div :class="mobileMenuOpen ? 'rotate-45 translate-y-1.5' : ''" class="w-6 h-0.5 bg-gray-300 transition-transform"></div>
                                <div :class="mobileMenuOpen ? 'opacity-0' : ''" class="w-4 h-0.5 bg-gray-300 transition-opacity"></div>
                                <div :class="mobileMenuOpen ? '-rotate-45 -translate-y-1.5' : ''" class="w-6 h-0.5 bg-gray-300 transition-transform"></div>
                            </button>

                            <div x-show="mobileMenuOpen" x-transition>
                                <livewire:nav.side />
                            </div>
                        </div>
                    @endif

                    <div class="relative flex items-center gap-6">
                        <!-- Notifications -->
                        <div @click="notificationOpen = !notificationOpen"
                             class="relative cursor-pointer p-2 rounded-full transition-all duration-200 flex items-center justify-center text-gray-400">
                            <i class="far fa-bell text-lg"></i>
                            <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full"></span>
                        </div>

                        <div x-show="notificationOpen"
                             @click.away="notificationOpen = false"
                             x-transition
                             class="fixed top-[61px] right-6 h-[90vh] w-96 px-6 bg-white shadow-lg border-l border-gray-200 z-50 rounded-2xl overflow-y-auto">
                            <livewire:notification />
                        </div>

                        @if(auth()->user() && (auth()->user()->role !== 'agency' && auth()->user()->role !== 'student'))
                            <a href="/message" class="relative" wire:navigate>
                                <i class="far fa-envelope text-gray-400 text-lg cursor-pointer"></i>
                                <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
                            </a>
                        @endif

                        <!-- Profile Dropdown -->
                        <div class="relative">
                            @if(auth()->user() && auth()->user()->role !== 'agency')
                                <button @click="profileOpen = !profileOpen" class="flex items-center gap-2 p-2 rounded-full">
                                    @if(auth()->user()->profile_picture)
                                        <img class="w-8 h-8 rounded-full object-cover" 
                                             src="{{ asset('storage/' . auth()->user()->profile_picture) }}" 
                                             alt="Profile">
                                    @else
                                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                            <span class="text-gray-600 text-sm font-medium">
                                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                </button>

                                <div x-show="profileOpen" 
                                     @click.away="profileOpen = false"
                                     x-transition
                                     class="absolute right-0 mt-4 w-44 bg-white shadow-lg rounded-lg overflow-hidden z-50">
                                    <a href="/profile" class="flex items-center px-4 py-2 text-gray-500 hover:bg-gray-100 border-b border-gray-200" wire:navigate>
                                        <i class="fas fa-user-circle mr-2"></i> Profile
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center w-full text-left px-4 py-2 text-gray-500 hover:bg-gray-100">
                                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                        </button>
                                    </form>
                                </div>
                            @else
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full text-left px-4 py-2 text-gray-500 hover:bg-gray-100">
                                        <i class="fas fa-sign-out-alt mr-2"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="ml-auto flex gap-4">
                        <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-gray-900">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="text-sm text-gray-700 hover:text-gray-900">Register</a>
                        @endif
                    </div>
                @endauth
            </div>
        </nav>
    @endif
    
    <style>
      [x-cloak] { display: none !important; }
    </style>
</header>