

<div>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    @foreach ($agencyList as $agency)
        <div 
            wire:click="openAgency({{ $agency->id }})"
            class="cursor-pointer p-4 bg-white rounded-lg shadow hover:bg-emerald-100 transition"
        >
            <div class="font-bold text-gray-700">{{ $agency->agency_name }}</div>
            <div class="text-sm text-gray-500">{{ $agency->contactPerson->name ?? 'No Name' }}</div>
        </div>
    @endforeach
</div>

@if ($showSlider)
<div class="fixed inset-0 bg-black bg-opacity-30 flex justify-center items-center z-50">
    <div class="bg-white w-full md:w-3/4 h-[90vh] rounded-lg shadow-lg p-6 overflow-y-auto">
        <div class="p-6">
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        
        <!-- Profile Picture Placeholder -->
        <div class="col-span-1 flex items-center justify-center bg-emerald-400 rounded-lg h-48">
            <!-- Profile Picture and Names -->
        <div class="col-span-1 flex flex-col items-center justify-center space-y-4">
            <!-- Circle Placeholder for Profile Pic -->
            <div class="w-24 h-24 rounded-full bg-emerald-400 flex items-center justify-center">
                <span class="text-white font-bold">Profile</span>
            </div>
        
            <!-- Contact Person Name -->
            <div class="text-center">
                <div class="text-lg font-semibold text-gray-700">{{ $contactPersonName }}</div>
                <div class="text-sm text-gray-500">{{ $agencyName }}</div>
            </div>
        </div>

        </div>

        <!-- Schedule Form -->
        <div class="col-span-4">
            @if ($selectedDay)
                <div class="bg-white rounded-lg shadow p-6 space-y-4 mb-6">
                    <h2 class="text-xl font-bold text-gray-700">Edit Schedule for {{ $selectedDay }}</h2>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-600 text-sm mb-1">Morning In</label>
                            <input type="time" wire:model.defer="expected_morning_in" class="w-full rounded border-gray-300" />
                        </div>

                        <div>
                            <label class="block text-gray-600 text-sm mb-1">Morning Out</label>
                            <input type="time" wire:model.defer="expected_morning_out" class="w-full rounded border-gray-300" />
                        </div>

                        <div>
                            <label class="block text-gray-600 text-sm mb-1">Afternoon In</label>
                            <input type="time" wire:model.defer="expected_afternoon_in" class="w-full rounded border-gray-300" />
                        </div>

                        <div>
                            <label class="block text-gray-600 text-sm mb-1">Afternoon Out</label>
                            <input type="time" wire:model.defer="expected_afternoon_out" class="w-full rounded border-gray-300" />
                        </div>

                        <div>
                            <label class="block text-gray-600 text-sm mb-1">Late Tolerance (optional)</label>
                            <input type="number" wire:model.defer="late_tolerance" min="0" class="form-input" placeholder="Late Tolerance (minutes)">
                        </div>
                        
                        <div>
                            <label class="block text-gray-600 text-sm mb-1">Grace Period (optional)</label>
                            <input type="number" wire:model.defer="grace_period" min="0" class="form-input" placeholder="Grace Period (minutes)">
                        </div>

                        <div>
                            <label class="block text-gray-600 text-sm mb-1">Overtime Allowed (optional)</label>
                            <input type="time" wire:model.defer="overtime_allowed" class="w-full rounded border-gray-300" />
                        </div>
                    </div>

                    <div class="text-right">
                        <button wire:click="save" class="px-6 py-2 bg-emerald-500 text-white rounded hover:bg-emerald-600 transition">Save</button>
                    </div>
                </div>
            @endif

            <!-- 7 Days Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach ($daysOfWeek as $day)
                <div class="relative">
                    <div 
                        wire:click="selectDay('{{ $day }}')"
                        class="border-2 {{ $selectedDay === $day ? 'border-emerald-500 bg-emerald-50' : 'border-dashed' }} rounded-lg p-6 flex flex-col items-center justify-center cursor-pointer hover:bg-gray-100 transition"
                    >
                        <div class="text-lg font-bold">{{ $day }}</div>
            
                        <div class="mt-2">
                            @if (isset($schedules[$day]))
                                <div class="text-emerald-500 text-3xl">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                            @else
                                <div class="text-gray-400 text-3xl">
                                    <i class="fas fa-plus"></i>
                                </div>
                            @endif
                        </div>
                    </div>
            
                    @if (isset($schedules[$day]))
                        <button 
                            wire:click.stop="confirmDelete('{{ $day }}')"
                            class="absolute top-2 right-2 text-red-500 hover:text-red-700"
                        >
                            <i class="fas fa-times-circle"></i>
                        </button>
                    @endif
                </div>
            @endforeach
            </div>
        </div>

    </div>

    <!-- Delete Confirmation Modal -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg w-80 text-center space-y-4 shadow-lg">
                <h2 class="text-lg font-bold text-gray-700">Delete Schedule?</h2>
                <p class="text-sm text-gray-500">Are you sure you want to delete the schedule for {{ $deleteDay }}?</p>

                <div class="flex justify-center gap-4 mt-6">
                    <button wire:click="$set('showDeleteModal', false)" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Cancel</button>
                    <button wire:click="delete" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Delete</button>
                </div>
            </div>
        </div>
    @endif
</div>

    </div>
</div>
@endif


</div>