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
}" class="p-6 w-full h-[100vh] flex flex-col overflow-hidden relative">
    <!-- Sliding Top Panel - Now positioned at the top -->
    <div
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 -translate-y-full"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-300 transform"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-full"
        class="absolute top-0 left-0 w-full bg-white shadow-xl z-50 p-6 rounded-b-lg"
    >
        <div class="relative">
            <button @click="closePanel()" class="absolute top-0 right-0 text-gray-600 hover:text-gray-800">
                <i class="fas fa-times"></i>
            </button>

            <div class="flex items-center mb-6">
                <div class="bg-emerald-100 rounded-lg p-2 mr-3">
                    <i class="fas fa-user text-emerald-500"></i>
                </div>
                <h2 class="text-xl font-semibold">{{ $isAdding ? 'Add New Coordinator' : 'Edit Coordinator' }}</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if ($isAdding)
                <div class="bg-amber-50 border-l-4 border-amber-500 p-4 mb-6 rounded-md md:col-span-2">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-amber-500 mr-2"></i>
                        <span class="text-sm font-medium text-amber-700">
                            Only one coordinator can be assigned per course for each active semester.
                        </span>
                    </div>
                </div>
                @endif

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" id="name" wire:model.defer="state.name"
                           class="border rounded-lg w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" />
                    @error('state.name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                @if (!$isAdding)
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" id="email" wire:model.defer="state.email"
                           @input="$wire.set('state.email', $event.target.value.endsWith('@gcc.com') ? $event.target.value : $event.target.value.split('@')[0] + '@gcc.com')"
                           class="border rounded-lg w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" />
                    @error('state.email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                @endif

                <div>
                    <label for="course_id" class="block text-sm font-medium text-gray-700 mb-1">Course Assignment</label>
                    <select id="course_id" wire:model.defer="state.course_id"
                            class="border rounded-lg w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white">
                        <option disabled value="">-- Select a course --</option>
                        @foreach ($availableCourses as $course)
                            <option value="{{ $course->id }}">{{ $course->abbreviation }} - {{ $course->full_name }}</option>
                        @endforeach
                    </select>
                    @error('state.course_id')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                  <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                  <input type="text" id="phone_number" wire:model.defer="state.phone_number"
                         placeholder="+639123456789 or 09123456789"
                         class="border rounded-lg w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" />
                  @error('state.phone_number') 
                      <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
                  @enderror
              </div>

                @if (!$isAdding)
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <input type="password" id="password" wire:model.defer="state.password"
                               class="border rounded-lg w-full py-2 pl-3 pr-10 text-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                               :type="$wire.showPassword ? 'text' : 'password'"
                               placeholder="Leave blank to keep current password" />
                        <button type="button" wire:click="$toggle('showPassword')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="fas {{ $showPassword ? 'fa-eye-slash' : 'fa-eye' }} text-gray-400"></i>
                        </button>
                    </div>
                </div>
                @endif

                <div class="md:col-span-2 flex justify-end gap-3">
                    <button @click="closePanel()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    @if ($isAdding)
                    <button wire:click="saveCoordinator()" class="bg-emerald-500 hover:bg-emerald-600 text-white font-medium py-2 px-4 rounded-lg transition flex items-center">
                        <i class="fas fa-plus mr-1"></i>
                        Add Coordinator
                    </button>
                    @else
                    <button wire:click="updateCoordinator()" class="bg-emerald-500 hover:bg-emerald-600 text-white font-medium py-2 px-4 rounded-lg transition flex items-center">
                        <i class="fas fa-save mr-1"></i>
                        Save Changes
                    </button>
                    <button wire:click="confirmDelete({{ $selectedCoordinatorId }})" class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg transition flex items-center">
                        <i class="fas fa-trash mr-1"></i>
                        Delete
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Content Container that will move down -->
    <div id="content-container" class="flex-1 relative transition-all duration-300 ease-in-out"
         :class="{'pt-[20rem]': isOpen}">
        <nav class="text-gray-600 mb-4">
            <span class="text-gray-500">Records</span> >
            <span class="text-gray-800">Coordinator</span>
        </nav>

        <div class="flex justify-between items-center mb-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Coordinator Management</h1>
                <p class="text-gray-500 text-sm">Manage coordinators and their assigned departments.</p>
            </div>
            <button @click="openPanel(true)" class="flex items-center gap-2 bg-emerald-500 text-white shadow-md px-4 py-2 rounded-lg hover:bg-emerald-600 transition">
              <i class="fas fa-plus mr-1"></i>
              Add Coordinator
          </button>
        </div>

        <div class="bg-white shadow-md rounded-lg p-4 overflow-auto">
            @foreach ($coordinators as $coordinator)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 items-center py-4 border-b last:border-none cursor-pointer hover:bg-gray-100 transition rounded-md"
     wire:click="openModalForEdit({{ $coordinator }})"
     @click="openPanel(false)">
                <div class="flex items-center mb-2 sm:mb-0">
                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600">
                        <i class="fas fa-user"></i>
                    </div>
                    <span class="ml-4 text-gray-800 font-semibold">{{ $coordinator->name }}</span>
                </div>
                <div class="flex items-center mb-2 sm:mb-0">
                    @php
                        $color = match($coordinator->course->abbreviation) {
                            'BSIT' => 'bg-emerald-500',
                            'BSCRIM' => 'bg-purple-500',
                            'BSBA' => 'bg-pink-500',
                            'BSA' => 'bg-yellow-500',
                            'BSED' => 'bg-blue-500',
                            'BEED' => 'bg-indigo-500',
                            'BPA' => 'bg-orange-500',
                            default => 'bg-gray-500',
                        };
                        $textColor = match($coordinator->course->abbreviation) {
                            'BSIT' => 'text-emerald-500',
                            'BSCRIM' => 'text-purple-500',
                            'BSBA' => 'text-pink-500',
                            'BSA' => 'text-yellow-500',
                            'BSED' => 'text-blue-500',
                            'BEED' => 'text-indigo-500',
                            'BPA' => 'text-orange-500',
                            default => 'text-gray-500',
                        };
                    @endphp
                    <div class="flex items-center px-3 py-1 border rounded-md border-gray-200 {{ $textColor }}">
                        <span class="w-3 h-3 rounded-full {{ $color }} mr-2"></span>
                        {{ $coordinator->course->abbreviation ?? 'N/A' }}
                    </div>
                </div>
                <div class="text-gray-500 text-sm truncate max-w-[200px] mb-2 sm:mb-0">
                    {{ $coordinator->course->full_name ?? 'N/A' }}
                </div>
                <div class="text-gray-500 text-sm flex items-center">
                    <i class="fas fa-envelope mr-2"></i>
                    {{ $coordinator->email }}
                </div>
            </div>
            @endforeach
        </div>
    </div>

    @if ($confirmingDeleteId)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl p-6 w-96 mx-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Confirm Deletion</h3>
            <p class="text-gray-600 mb-6">Are you sure you want to delete this coordinator? This action cannot be undone.</p>
            <div class="flex justify-end gap-3">
                <button wire:click="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button wire:click="deleteCoordinator()" class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg transition flex items-center">
                    <i class="fas fa-trash mr-1"></i>
                    Delete
                </button>
            </div>
        </div>
    </div>
    @endif
</div>