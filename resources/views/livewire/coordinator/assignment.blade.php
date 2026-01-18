<div x-data="{ filter: 'all', showAssignPanel: @entangle('showAssignPanel').live, selectedStudent: @entangle('selectedStudent').live,
    search: @entangle('search').live }" class="p-6 w-full">
    <div class="flex space-x-4 border-b">
        <button @click="filter = 'all'" :class="filter === 'all' ? 'border-emerald-500 text-emerald-600' : 'text-gray-600'"
            class="py-2 px-6 border-b-2 border-transparent focus:outline-none">All</button>
        <button @click="filter = 'enrolled'" :class="filter === 'enrolled' ? 'border-emerald-500 text-emerald-600' : 'text-gray-600'"
            class="py-2 px-6 border-b-2 border-transparent focus:outline-none">Enrolled</button>
        <button @click="filter = 'non-enrolled'" :class="filter === 'non-enrolled' ? 'border-emerald-500 text-emerald-600' : 'text-gray-600'"
            class="py-2 px-6 border-b-2 border-transparent focus:outline-none">Non-Enrolled</button>
    </div>

    <div class="flex mt-4">
        <div :class="showAssignPanel ? 'w-3/5' : 'w-full'" class="transition-all duration-300">
            <div class="relative mt-4 mb-4">
                <input
                    type="text"
                    class="w-full pl-4 pr-10 py-3 rounded-full bg-gray-100 text-gray-700 focus:ring focus:ring-emerald-300 focus:outline-none"
                    placeholder="Search..."
                    x-model="search"
                />
            </div>
            <div class="mt-4">
                <label class="text-sm text-gray-600">Total OJT Hours (HH:MM)</label>
                <div class="flex items-center space-x-2">
                    <input
                        type="text"
                        class="w-24 px-2 py-1 border rounded-md focus:outline-none"
                        wire:model.lazy="selectedHours"
                        placeholder="{{ \Carbon\CarbonInterval::hours(floor($courseHoursDecimal))->minutes(($courseHoursDecimal - floor($courseHoursDecimal)) * 60)->format('%H:%I') }}"
                    >
                    <button
                        wire:click="saveSelectedHours"
                        class="px-3 py-1 bg-emerald-500 text-white text-sm rounded-md"
                    >
                        Save
                    </button>
                </div>
            </div>
            <template x-if="filter === 'all' || filter === 'enrolled'">
                <div>
                    <p class="font-bold text-gray-700 mb-2">Enrolled</p>
                    <div class="space-y-4">
                        @forelse ($enrolledStudents as $student)
                            <div
                                class="p-4 bg-white rounded-lg shadow flex items-center justify-between"
                                x-show="'{{ strtolower($student->name . ' ' . $student->id . ' ' . $student->phone_number) }}'.includes(search.toLowerCase())"
                            >
                                <div class="flex items-center space-x-4">
                                    <span class="h-12 w-12 bg-gray-300 rounded-full"></span>
                                    <div>
                                        <p class="font-semibold text-gray-700">{{ $student->name }}</p>
                                        <p class="text-sm text-gray-500">ID: {{ $student->id }}</p>
                                        <p class="text-sm text-gray-500">Phone: {{ $student->phone_number }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 px-3 py-1 bg-gray-100 rounded-full">
                                    <span class="h-8 w-8 bg-gray-400 rounded-full"></span>
                                    <p class="text-sm text-gray-700">{{ $student->agency->agency_name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500">No enrolled students.</p>
                        @endforelse
                    </div>
                </div>
            </template>

            <template x-if="filter === 'all' || filter === 'non-enrolled'">
                <div class="mt-6">
                    <p class="font-bold text-gray-700 mb-2">Non-Enrolled</p>
                    <div class="space-y-4">
                        @forelse ($nonEnrolledStudents as $student)
                            <div
                                class="p-4 bg-white rounded-lg shadow flex items-center justify-between"
                                x-show="'{{ strtolower($student->name . ' ' . $student->id . ' ' . $student->phone_number) }}'.includes(search.toLowerCase())"
                            >
                                <div class="flex items-center space-x-4">
                                    <span class="h-12 w-12 bg-gray-300 rounded-full"></span>
                                    <div>
                                        <p class="font-semibold text-gray-700">{{ $student->name }}</p>
                                        <p class="text-sm text-gray-500">ID: {{ $student->id }}</p>
                                        <p class="text-sm text-gray-500">Phone: {{ $student->phone_number }}</p>
                                    </div>
                                </div>
                                <button @click="showAssignPanel = true; selectedStudent = '{{ $student->name }}'; $wire.set('selectedStudentId', '{{ $student->id }}')"
                                    class="px-4 py-2 bg-emerald-500 text-white rounded-md">
                                    Assign
                                </button>
                            </div>
                        @empty
                            <p class="text-gray-500">No non-enrolled students.</p>
                        @endforelse
                    </div>
                </div>
            </template>
        </div>

        <div x-show="showAssignPanel" class="w-2/5 bg-white rounded-lg shadow p-6 transition-all duration-300">
            <h2 class="text-lg font-semibold text-gray-700">Assign Agency</h2>

            <div class="mt-4">
                <label class="text-sm text-gray-600">Name</label>
                <div class="flex items-center space-x-2">
                    <span class="h-8 w-8 bg-yellow-400 rounded-full flex items-center justify-center text-white">
                        <i class="fas fa-user"></i>
                    </span>
                    <input type="text" class="w-full border-b border-gray-300 focus:outline-none" x-model="selectedStudent" readonly>
                </div>
            </div>

            <div class="mt-4">
              <label class="block text-sm font-medium text-gray-700">Select Agency</label>
              <select wire:model="agency_id" class="mt-1 block w-full py-2 px-3 border rounded-md shadow-sm focus:outline-none">
                  <option value="">-- Select Agency --</option>
                  @foreach ($agencies as $agency)
                      <option value="{{ $agency->id }}" 
                          @if ($agency->slot === null || $agency->slot <= 0) disabled @endif>
                          {{ $agency->agency_name }} ({{ $agency->slot ?? 0 }} slots)
                      </option>
                  @endforeach
              </select>
          </div>


            <div class="mt-6">
                <button wire:click="assignAgency" class="w-full bg-emerald-500 text-white py-2 rounded-md">Assign</button>
            </div>

            <div class="mt-4 text-center">
                <button @click="showAssignPanel = false" class="text-sm text-gray-500">Cancel</button>
            </div>

            @if (session()->has('success'))
                <div class="mt-4 text-green-500">{{ session('success') }}</div>
            @endif
        </div>
    </div>
</div>