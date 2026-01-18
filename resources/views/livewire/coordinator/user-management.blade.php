<div x-data="{
    selectedUser: null,
    isEditing: false,
    openCreate: false,
    search: '',
    activeFilter: '{{ $activeFilter }}', // Initialize activeFilter from Livewire
    users: @entangle('users').live, // Entangle the users data and update live
    isMobile: window.innerWidth < 768
}"
     x-init="() => {
        window.addEventListener('resize', () => {
            isMobile = window.innerWidth < 768;
        });
    }"
     class="flex flex-col h-full w-full bg-gray-100 md:flex-row">

    <div class="w-full bg-white shadow-md p-4 md:hidden">
        <p class="text-gray-600 text-sm">
            <span class="text-blue-500">Student Management</span>
            <span x-show="selectedUser" class="text-blue-500" x-text="`> ${selectedUser ? selectedUser.name : ''}`"></span>
        </p>
    </div>

    <div :class="{
        'md:w-1/4': !isMobile,
        'w-full': isMobile,
        'bg-white': true,
        'shadow-md': true,
        'p-4': true,
        'transition-all': true,
        'duration-300': true,
        'flex-none': !selectedUser,
        'w-1/6': !selectedUser,
    }">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Student Filters</h2>
        </div>

        <div class="relative mb-4">
            <input
                type="text"
                class="w-full pl-10 py-3 rounded-full bg-gray-100 text-gray-700 focus:ring focus:ring-emerald-300 focus:outline-none"
                placeholder="Search students..."
                x-model="search"
            />
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <i class="fas fa-search text-gray-500"></i>
            </div>
        </div>

        <ul :class="{'space-y-2': !isMobile, 'flex space-x-2': isMobile}">
            <li>
                <button @click="selectedUser = null; $wire.loadStudents('approved'); activeFilter = 'approved'"
                        :class="{'bg-emerald-200 text-emerald-700': activeFilter === 'approved', 'w-full text-left p-2 rounded-md hover:bg-gray-200 bg-white shadow-md': !isMobile, 'px-4 py-2 rounded-md hover:bg-gray-100 bg-white shadow-sm text-sm': isMobile}">
                    Approved
                </button>
            </li>
            <li>
                <button @click="selectedUser = null; $wire.loadStudents('intern'); activeFilter = 'intern'"
                        :class="{'bg-emerald-200 text-emerald-700': activeFilter === 'intern', 'w-full text-left p-2 rounded-md hover:bg-gray-200 bg-white shadow-md': !isMobile, 'px-4 py-2 rounded-md hover:bg-gray-100 bg-white shadow-sm text-sm': isMobile}">
                    Interns
                </button>
            </li>
            <li>
                <button @click="selectedUser = null; $wire.loadStudents('pending'); activeFilter = 'pending'"
                        :class="{'bg-emerald-200 text-emerald-700': activeFilter === 'pending', 'w-full text-left p-2 rounded-md hover:bg-gray-200 bg-white shadow-md': !isMobile, 'px-4 py-2 rounded-md hover:bg-gray-100 bg-white shadow-sm text-sm': isMobile}">
                    Pending Students
                </button>
            </li>
            <li>
                <button @click="selectedUser = null; $wire.loadStudents('completed'); activeFilter = 'completed'"
                        :class="{'bg-emerald-200 text-emerald-700': activeFilter === 'completed', 'w-full text-left p-2 rounded-md hover:bg-gray-200 bg-white shadow-md': !isMobile, 'px-4 py-2 rounded-md hover:bg-gray-100 bg-white shadow-sm text-sm': isMobile}">
                    Completed
                </button>
            </li>
            <li>
                <button @click="selectedUser = null; $wire.loadStudents('incomplete'); activeFilter = 'incomplete'"
                        :class="{'bg-emerald-200 text-emerald-700': activeFilter === 'incomplete', 'w-full text-left p-2 rounded-md hover:bg-gray-200 bg-white shadow-md': !isMobile, 'px-4 py-2 rounded-md hover:bg-gray-100 bg-white shadow-sm text-sm': isMobile}">
                    Incomplete
                </button>
            </li>
            <li>
                <button @click="selectedUser = null; $wire.loadStudents('rejected'); activeFilter = 'rejected'"
                        :class="{'bg-emerald-200 text-emerald-700': activeFilter === 'rejected', 'w-full text-left p-2 rounded-md hover:bg-gray-200 bg-white shadow-md': !isMobile, 'px-4 py-2 rounded-md hover:bg-gray-100 bg-white shadow-sm text-sm': isMobile}">
                    Rejected
                </button>
            </li>
        </ul>
    </div>

    <div class="flex-1 bg-white shadow-md p-4 transition-all duration-300 max-w-full md:flex md:flex-col"
         :class="selectedUser ? 'md:w-2/3' : 'md:w-full'">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Student List</h2>
            <button @click="openCreate = true" class="px-4 py-2 bg-[#258967] text-white rounded-md">
                <i class="fas fa-plus mr-2"></i> Add Student
            </button>
        </div>
        <ul class="space-y-3">
            @forelse ($users as $user)
                <li
                    class="flex items-center justify-between p-3 bg-white rounded-lg shadow-md shadow-gray-200 hover:bg-gray-50 transition"
                    x-show="search === '' || user.name.toLowerCase().includes(search.toLowerCase()) || user.email.toLowerCase().includes(search.toLowerCase())"
                >
                    <button @click="selectedUser = {
                    id: {{ $user->id }},
                            name: '{{ $user->name }}',
                            email: '{{ $user->email }}',
                            status: '{{ $user->status }}',
                            deleted_at: {{ $user->deleted_at ? 'true' : 'false' }}
                        }; isEditing = false"
                 class="flex items-center w-full text-left">
                        <div class="w-10 h-10 bg-gray-200 rounded-full mr-4 flex items-center justify-center text-gray-500 text-sm font-semibold">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">{{ $user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                        </div>
                    </button>
                    <div class="flex items-center gap-2 ml-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if ($user->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif ($user->status === 'approved') bg-green-100 text-green-800
                                    @elseif ($user->status === 'completed') bg-blue-100 text-blue-800
                                    @elseif ($user->status === 'incomplete') bg-red-100 text-red-800
                                    @elseif ($user->status === 'ongoing') bg-indigo-100 text-indigo-800
                                    @elseif ($user->deleted_at) bg-red-300 text-red-800 @endif
                                    ">
                            {{ $user->deleted_at ? 'Rejected' : ($user->status === 'ongoing' ? 'Intern' : ucfirst($user->status)) }}
                        </span>
                        @if ($user->status === 'pending' && !$user->deleted_at)
                            <button wire:click="acceptUser({{ $user->id }})" class="text-green-500 hover:text-green-700">
                                <i class="fas fa-check"></i>
                            </button>
                            <button wire:click="rejectUser({{ $user->id }})" class="text-red-400 hover:text-red-600">
                                <i class="fas fa-times"></i>
                            </button>
                        @elseif ($user->deleted_at)
                            <button wire:click="restoreUser({{ $user->id }})" class="text-blue-500 hover:text-blue-700">
                                <i class="fas fa-undo"></i>
                            </button>
                            <button wire:click="deleteUser({{ $user->id }})" class="text-red-500 hover:text-red-700">
                                <i class="fas fa-trash"></i>
                            </button>
                        @else
                            <button wire:click="deleteUser({{ $user->id }})" class="text-red-500 hover:text-red-700">
                                <i class="fas fa-trash"></i>
                            </button>
                        @endif
                    </div>
                </li>
            @empty
                <li>
                    <div class="p-3 bg-white rounded-lg shadow-md shadow-gray-200">
                        No students found for the current filter.
                    </div>
                </li>
            @endforelse
        </ul>
    </div>

    <div x-show="selectedUser" x-transition class="bg-white shadow-md p-6 transition-all duration-300 relative md:w-1/3">
        <button @click="selectedUser = null; isEditing = false;" class="absolute top-4 right-4 text-gray-500 text-2xl font-bold">
    &times;
</button>
@if ($selectedUser)
    <div class="flex flex-col space-y-4">
        <div class="flex items-center space-x-4">
            <div class="w-20 h-20 bg-gray-300 rounded-full flex items-center justify-center text-gray-500 text-2xl font-semibold">
                {{ strtoupper(substr($selectedUser['name'], 0, 2)) }}
            </div>
            <div>
                <h2 class="text-xl font-semibold text-gray-800">{{ $selectedUser['name'] }}</h2>
                <p class="text-sm text-gray-500"><i class="fas fa-envelope mr-1"></i> {{ $selectedUser['email'] }}</p>
                <p class="text-sm text-gray-500"><i class="fas fa-id-card mr-1"></i> Student ID: {{ $selectedUser['student_id'] ?? 'N/A' }}</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1
                              @if ($selectedUser['status'] === 'pending') bg-yellow-100 text-yellow-800
                              @elseif ($selectedUser['status'] === 'approved') bg-green-100 text-green-800
                              @elseif ($selectedUser['status'] === 'completed') bg-blue-100 text-blue-800
                              @elseif ($selectedUser['status'] === 'incomplete') bg-red-100 text-red-800
                              @elseif ($selectedUser['status'] === 'ongoing') bg-indigo-100 text-indigo-800
                              @elseif ($selectedUser['deleted_at']) bg-red-300 text-red-800 @endif
                              ">
                    {{ $selectedUser['deleted_at'] ? 'Rejected' : ($selectedUser['status'] === 'ongoing' ? 'Intern' : ucfirst($selectedUser['status'])) }}
                </span>
            </div>
        </div>

        <div>
            <h3 class="text-md font-semibold text-gray-700"><i class="fas fa-info-circle mr-1"></i> Details</h3>
            <ul class="text-sm text-gray-600 space-y-1 mt-2">
                <li><i class="fas fa-user-tag mr-1"></i> Role: {{ ucfirst($selectedUser['role']) }}</li>
                <li><i class="fas fa-graduation-cap mr-1"></i> Course: {{ $selectedUser['course']['full_name'] ?? 'N/A' }} ({{ $selectedUser['course']['abbreviation'] ?? 'N/A' }})</li>
                <li><i class="fas fa-level-up-alt mr-1"></i> Year Level: {{ $selectedUser['year_level']['name'] ?? 'N/A' }}</li>
                @if ($selectedUser['phone_number'])
                    <li><i class="fas fa-phone mr-1"></i> Phone: {{ $selectedUser['phone_number'] }}</li>
                @endif
            </ul>
        </div>

        @if ($selectedUser['school_id_image'])
            <div>
                <h3 class="text-md font-semibold text-gray-700"><i class="fas fa-image mr-1"></i> School ID Image</h3>
                <img src="{{ asset('storage/' . $selectedUser['school_id_image']) }}" alt="School ID" class="mt-2 rounded" style="max-width: 100%;">
            </div>
        @else
            <p class="text-sm text-gray-600"><i class="fas fa-image mr-1"></i> No School ID Image Available</p>
        @endif
    </div>
@endif
    </div>

    <div x-show="openCreate" x-transition class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/3 relative">
            <button @click="openCreate = false" class="absolute top-2 right-2 text-gray-500 text-2xl font-bold">
                &times;
            </button>
            <h2 class="text-lg font-semibold mb-4"><i class="fas fa-user-plus mr-2"></i> Create New Student</h2>
            <form wire:submit.prevent="createUser">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name:</label>
                    <input type="text" wire:model="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                    <input type="email" wire:model="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email">
                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password:</label>
                    <input type="password" wire:model="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="password">
                    @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label for="role" class="block text-gray-700 text-sm font-bold mb-2">Role:</label>
                    <select wire:model="role" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="role">
                        <option value="student">Student</option>
                        <option value="intern">Intern</option>
                    </select>
                    @error('role') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label for="course" class="block text-gray-700 text-sm font-bold mb-2">Course:</label>
                    <select wire:model="newStudentCourseId" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="course">
                        <option value="">Select Course</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->full_name }} ({{ $course->abbreviation }})</option>
                        @endforeach
                    </select>
                    @error('newStudentCourseId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="flex items-center justify-between">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                        <i class="fas fa-user-plus mr-2"></i> Create Student
                    </button>
                    <button @click="openCreate = false" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>