<div x-data="{ 
    isOpen: @entangle('isOpen'),
    openPanel(isAdding = true) { 
        @this.set('isAdding', isAdding);
        if (isAdding) {
            @this.call('resetState');
        }
        this.isOpen = true;
    },
    closePanel() { 
        this.isOpen = false;
    }
}" class="p-6 w-full flex">
    <!-- Main Content (Left Side) -->
    <div class="flex-1 transition-all duration-300 ease-in-out"
         :class="{'mr-[30rem]': isOpen}">
        
        <!-- Breadcrumb Navigation -->
        <nav class="text-gray-600 mb-4">
            <span class="text-gray-500">Records</span> > 
            <span class="text-gray-800">Student</span>
        </nav>

        <!-- Page Title & Create Button -->
        <div class="flex justify-between items-center mb-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Student Management</h1>
                <p class="text-gray-500 text-sm">Manage students and their assigned courses.</p>
            </div>
            <button @click="openPanel(true)" 
                    class="flex items-center gap-2 bg-white shadow-md px-4 py-2 rounded-3xl text-gray-700 hover:bg-gray-100 transition">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                Create Student
            </button>
        </div>

        <!-- Student List (Auto Adjustable Width) -->
        <div class="bg-white rounded-md p-4">
            @foreach ($students as $student)
            <div class="grid grid-cols-[1fr_0.5fr_0.5fr_0.5fr_auto] items-center py-4 border-b last:border-none cursor-pointer hover:bg-gray-50 transition"
                 wire:click="openModalForEdit({{ $student }})"
                 @click="openPanel(false)">
                
                <!-- Profile Picture + Name -->
                <div class="flex items-center gap-6">
                    @if($student->profile_picture)
                        <img class="w-10 h-10 rounded-full object-cover" 
                             src="{{ asset('storage/' . $student->profile_picture) }}" 
                             alt="Profile">
                    @else
                        <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center">
                            <span class="text-gray-600 text-sm font-medium">
                                {{ strtoupper(substr($student->name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                    <span class="text-gray-800 font-semibold">{{ $student->name }}</span>
                </div>
        
                <!-- Student ID -->
                <div class="text-gray-700 font-medium">{{ $student->student_id }}</div>
        
                <!-- Contact Number -->
                <div class="text-gray-500 text-sm">{{ $student->phone_number }}</div>
        
                <!-- Course Tag -->
                <div class="flex items-center">
                    <div class="flex items-center px-3 py-1 border rounded-md text-gray-700">
                        @php
                            $color = match($student->course->abbreviation) {
                                'BSIT' => 'bg-green-500',
                                'BSCRIM' => 'bg-blue-500',
                                'BSBA' => 'bg-pink-500',
                                'BSED' => 'bg-yellow-500',
                                'BSHRM' => 'bg-purple-500',
                                default => 'bg-gray-500',
                            };
                        @endphp
                        <span class="w-2 h-2 rounded-full {{ $color }} mr-2"></span> 
                        {{ $student->course->abbreviation ?? 'N/A' }}
                    </div>
                </div>
        
                <!-- Year Level -->
                <div class="text-gray-500 text-sm">{{ $student->yearLevel->level ?? 'N/A' }}</div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Form Panel (Right Side) -->
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-x-full"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-transition:leave="transition ease-in duration-300 transform"
         x-transition:leave-start="opacity-100 translate-x-0"
         x-transition:leave-end="opacity-0 translate-x-full"
         class="fixed right-0 top-0 h-full w-[30rem] bg-white shadow-xl z-40 p-6 border-l border-gray-200 overflow-y-auto">
        
        <div class="relative">
            <button @click="closePanel()" class="absolute top-0 right-0 text-gray-600 hover:text-gray-800">
                <i class="fas fa-times"></i>
            </button>

            <div class="flex items-center mb-6">
                <div class="bg-blue-100 rounded-lg p-2 mr-3">
                    <i class="fas fa-user-graduate text-blue-500"></i>
                </div>
                <h2 class="text-xl font-semibold">{{ $isAdding ? 'Add New Student' : 'Edit Student' }}</h2>
            </div>

            <div class="space-y-4">
                <!-- Profile Picture -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Profile Picture</label>
                    <div class="flex items-center gap-4">
                        @if($state['profile_picture'])
                            <img class="w-16 h-16 rounded-full object-cover" 
                                 src="{{ $state['profile_picture']->temporaryUrl() }}" 
                                 alt="Preview">
                        @elseif(!$isAdding && $selectedStudentId)
                            @php $student = \App\Models\User::find($selectedStudentId); @endphp
                            @if($student && $student->profile_picture)
                                <img class="w-16 h-16 rounded-full object-cover" 
                                     src="{{ asset('storage/' . $student->profile_picture) }}" 
                                     alt="Current Profile">
                            @else
                                <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-user text-gray-400 text-xl"></i>
                                </div>
                            @endif
                        @else
                            <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-user text-gray-400 text-xl"></i>
                            </div>
                        @endif
                        <input type="file" id="profile_picture" wire:model="state.profile_picture"
                               class="border rounded-lg w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    @error('state.profile_picture') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Full Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" id="name" wire:model.defer="state.name"
                           class="border rounded-lg w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('state.name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Student ID -->
                <div>
                    <label for="student_id" class="block text-sm font-medium text-gray-700 mb-1">Student ID</label>
                    <input type="text" id="student_id" wire:model.defer="state.student_id"
                           class="border rounded-lg w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('state.student_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Course Assignment -->
                <div>
                    <label for="course_id" class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                    <select id="course_id" wire:model.defer="state.course_id"
                            class="border rounded-lg w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                        <option value="">-- Select a course --</option>
                        @foreach ($availableCourses as $course)
                            <option value="{{ $course->id }}">{{ $course->abbreviation }} - {{ $course->full_name }}</option>
                        @endforeach
                    </select>
                    @error('state.course_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Year Level -->
                <div>
                    <label for="year_level_id" class="block text-sm font-medium text-gray-700 mb-1">Year Level</label>
                    <select id="year_level_id" wire:model.defer="state.year_level_id"
                            class="border rounded-lg w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                        <option value="">-- Select year level --</option>
                        @foreach ($yearLevels as $yearLevel)
                            <option value="{{ $yearLevel->id }}">{{ $yearLevel->level }}</option>
                        @endforeach
                    </select>
                    @error('state.year_level_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Phone Number -->
                <div>
                    <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="text" id="phone_number" wire:model.defer="state.phone_number"
                           placeholder="+639123456789 or 09123456789"
                           class="border rounded-lg w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('state.phone_number') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Email (only for editing) -->
                @if(!$isAdding)
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" id="email" wire:model.defer="state.email" readonly
                           class="border rounded-lg w-full py-2 px-3 text-gray-700 bg-gray-100 cursor-not-allowed">
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3 pt-4">
                    <button @click="closePanel()" 
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    @if($isAdding)
                    <button wire:click="saveStudent()" 
                            class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition flex items-center">
                        <i class="fas fa-plus mr-1"></i>
                        Add Student
                    </button>
                    @else
                    <button wire:click="updateStudent()" 
                            class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition flex items-center">
                        <i class="fas fa-save mr-1"></i>
                        Save Changes
                    </button>
                    <button wire:click="confirmDelete({{ $selectedStudentId }})" 
                            class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg transition flex items-center">
                        <i class="fas fa-trash mr-1"></i>
                        Delete
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    @if($confirmingDeleteId)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl p-6 w-96 mx-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Confirm Deletion</h3>
            <p class="text-gray-600 mb-6">Are you sure you want to delete this student? This action cannot be undone.</p>
            <div class="flex justify-end gap-3">
                <button wire:click="closeModal()" 
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button wire:click="deleteStudent()" 
                        class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg transition flex items-center">
                    <i class="fas fa-trash mr-1"></i>
                    Delete
                </button>
            </div>
        </div>
    </div>
    @endif
</div>