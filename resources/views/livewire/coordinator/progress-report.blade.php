<div class="h-full w-full p-4 md:p-8 relative" x-data="{ selectedStudent: null, showMobilePanel: false }">
    <div class="flex justify-between items-center mb-6 md:mb-10">
        <div class="flex flex-col">
            <h2 class="text-2xl md:text-3xl font-extrabold text-gray-800 tracking-tight">Student Progress Overview</h2>
            @if ($course)
                <h3 class="text-base md:text-lg font-semibold text-gray-600 mt-1 md:mt-2">Course: <span class="text-emerald-700">{{ $course->full_name }}</span></h3>
            @else
                <h3 class="text-base md:text-lg font-semibold text-gray-600 mt-1 md:mt-2">No Course Assigned</h3>
            @endif
        </div>
        <div class="space-x-2">
            <button onclick="window.print()"
                    class="inline-flex items-center bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-3 md:py-3 md:px-4 rounded-md shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-400 transition duration-150 ease-in-out">
                <i class="fas fa-print mr-1 md:mr-2 text-sm md:text-base"></i>
                <span class="hidden sm:inline">Print Report</span>
            </button>
        </div>
    </div>

    <div class="flex flex-col md:flex-row transition-all duration-300">
        <div :class="selectedStudent ? 'w-full md:w-2/3 md:pr-8' : 'w-full'" class="transition-all duration-300">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-4 md:px-6 py-3 md:py-4 border-b border-gray-200 font-semibold text-gray-700 flex items-center">
                    <i class="fas fa-list-ul mr-2 md:mr-3 text-gray-500"></i> Student List
                    <span class="ml-auto text-xs md:text-sm text-gray-500">{{ count($students) }} Students</span>
                </div>
                <div class="p-2 md:p-4 divide-y divide-gray-200">
                    @forelse ($students as $student)
                        <div @click="selectedStudent = {{ $student->id }}; $wire.showStudentDetails({{ $student->id }}); 
                            if (window.innerWidth < 768) { showMobilePanel = true }"
                             class="py-2 md:py-3 flex items-center hover:bg-gray-50 cursor-pointer transition duration-150 ease-in-out">
                            <div class="flex-1 min-w-0">
                                <p class="text-base md:text-lg font-medium text-gray-800 truncate">{{ $student->name }}</p>
                                <p class="text-xs md:text-sm text-gray-500 truncate">ID: {{ $student->student_id }}</p>
                                @if ($student->agency)
                                    <p class="text-xs md:text-sm text-emerald-600 truncate">Agency: {{ $student->agency->agency_name }}</p>
                                @else
                                    <p class="text-xs md:text-sm text-gray-500 truncate">Agency: Not Assigned</p>
                                @endif
                            </div>
                            <div class="ml-2 md:ml-4 flex-shrink-0 w-24 md:w-32">
                                <div class="relative pt-1">
                                    @php
                                        $remainingHours = $student->remaining_hours ?? 0;
                                        $totalHours = $course->total_hours ?? 1;
                                        $completedHours = max(0, $totalHours - $remainingHours);
                                        $progressPercentage = ($totalHours > 0) ? min(100, ($completedHours / $totalHours) * 100) : 0;
                                    @endphp
                                    <div class="overflow-hidden h-2 text-xs flex rounded bg-emerald-100">
                                        <div style="width: {{ $progressPercentage }}%"
                                             class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-emerald-500 transition-all duration-300 rounded"></div>
                                    </div>
                                    <div class="flex justify-between text-gray-600 text-xs mt-1">
                                        <span>{{ $completedHours }} Hrs</span>
                                        <span>{{ $totalHours }} Total</span>
                                    </div>
                                </div>
                            </div>
                            <div class="ml-2 text-gray-400">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </div>
                    @empty
                        <div class="py-4 md:py-6 text-center text-gray-500">
                            <i class="fas fa-exclamation-triangle text-lg md:text-xl mb-1"></i>
                            <p class="text-sm md:text-base">No students found for this course.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Mobile Panel (Modal) -->
        <div x-show="showMobilePanel" x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-full" x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-full"
             class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 md:hidden"
             @click.self="showMobilePanel = false">
            <div class="relative min-h-screen flex items-end justify-center p-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true"></div>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white p-4">
                        <div class="flex justify-end mb-4">
                            <button @click="showMobilePanel = false"
                                    class="text-gray-500 hover:text-gray-700 focus:outline-none transition duration-150 ease-in-out">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>
                        @if ($selectedStudentDetails)
                            <div class="flex flex-col items-center mb-6">
                                <div class="relative w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                                    <span class="text-xl font-semibold text-gray-700">{{ strtoupper(substr($selectedStudentDetails->name, 0, 2)) }}</span>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-800 mt-2">{{ $selectedStudentDetails->name }}</h3>
                                <p class="text-sm text-gray-500">ID: {{ $selectedStudentDetails->student_id }}</p>
                            </div>

                            <div class="mb-4">
                                <h4 class="text-lg font-semibold text-emerald-700 mb-2"><i class="fas fa-briefcase mr-2"></i> Internship Details</h4>
                                <p class="text-gray-600 text-sm mb-1">Agency: <span class="font-medium">{{ $selectedStudentDetails->agency->agency_name ?? 'Not Assigned' }}</span></p>
                                <p class="text-gray-600 text-sm mb-1">Course: <span class="font-medium">{{ $course->full_name ?? 'N/A' }}</span></p>
                            </div>

                            <div class="mb-6">
                                <h4 class="text-lg font-semibold text-emerald-700 mb-2"><i class="fas fa-chart-pie mr-2"></i> Progress Overview</h4>
                                <div class="relative w-32 h-32 mx-auto">
                                    <svg class="w-full h-full" viewBox="0 0 100 100">
                                        @php
                                            $remainingHours = $selectedStudentDetails->remaining_hours ?? 0;
                                            $totalHours = $course->total_hours ?? 1;
                                            $completedHours = max(0, $totalHours - $remainingHours);
                                            $progressPercentage = ($totalHours > 0) ? min(100, ($completedHours / $totalHours) * 100) : 0;
                                            $dashOffset = 251.2 - ($progressPercentage / 100 * 251.2);
                                        @endphp
                                        <circle class="text-emerald-200" stroke-width="10" stroke="currentColor" fill="transparent" r="40" cx="50" cy="50"/>
                                        <circle class="text-emerald-500" stroke-width="10" stroke-linecap="round" stroke="currentColor" fill="transparent" r="40" cx="50" cy="50"
                                                stroke-dasharray="251.2"
                                                stroke-dashoffset="{{ $dashOffset }}">
                                        </circle>
                                    </svg>
                                    <p class="absolute inset-0 flex items-center justify-center text-lg font-semibold text-emerald-700">
                                        {{ round($progressPercentage) }}%
                                    </p>
                                </div>
                                <div class="bg-emerald-100 rounded-md p-3 md:p-4 mt-2">
                                    <p class="text-emerald-700 text-sm">Completed Hours: <span class="font-semibold">{{ $completedHours }}</span> / {{ $totalHours }}</p>
                                    <p class="text-emerald-700 text-sm mt-1">Remaining Hours: <span class="font-semibold">{{ $remainingHours }}</span></p>
                                </div>
                            </div>

                            <div>
                                <h4 class="text-lg font-semibold text-gray-700 mb-2"><i class="fas fa-info-circle mr-2"></i> Additional Information</h4>
                                <p class="text-gray-600 text-sm">More detailed information about the student or their progress could be displayed here. This could include reports, feedback logs, etc.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Desktop Panel -->
        <div x-show="selectedStudent && !showMobilePanel" class="hidden md:block w-full md:w-1/3 bg-white shadow-lg rounded-lg p-4 md:p-6 transition-all duration-300">
            <div class="flex justify-end mb-4">
                <button @click="selectedStudent = null; $wire.selectedStudentDetails = null"
                        class="text-gray-500 hover:text-gray-700 focus:outline-none transition duration-150 ease-in-out">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            @if ($selectedStudentDetails)
                <div class="flex flex-col items-center mb-6">
                    <div class="relative w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                        <span class="text-xl font-semibold text-gray-700">{{ strtoupper(substr($selectedStudentDetails->name, 0, 2)) }}</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mt-2">{{ $selectedStudentDetails->name }}</h3>
                    <p class="text-sm text-gray-500">ID: {{ $selectedStudentDetails->student_id }}</p>
                </div>

                <div class="mb-4">
                    <h4 class="text-lg font-semibold text-emerald-700 mb-2"><i class="fas fa-briefcase mr-2"></i> Internship Details</h4>
                    <p class="text-gray-600 text-sm mb-1">Agency: <span class="font-medium">{{ $selectedStudentDetails->agency->agency_name ?? 'Not Assigned' }}</span></p>
                    <p class="text-gray-600 text-sm mb-1">Course: <span class="font-medium">{{ $course->full_name ?? 'N/A' }}</span></p>
                </div>

                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-emerald-700 mb-2"><i class="fas fa-chart-pie mr-2"></i> Progress Overview</h4>
                    <div class="relative w-32 h-32 mx-auto">
                        <svg class="w-full h-full" viewBox="0 0 100 100">
                            @php
                                $remainingHours = $selectedStudentDetails->remaining_hours ?? 0;
                                $totalHours = $course->total_hours ?? 1;
                                $completedHours = max(0, $totalHours - $remainingHours);
                                $progressPercentage = ($totalHours > 0) ? min(100, ($completedHours / $totalHours) * 100) : 0;
                                $dashOffset = 251.2 - ($progressPercentage / 100 * 251.2);
                            @endphp
                            <circle class="text-emerald-200" stroke-width="10" stroke="currentColor" fill="transparent" r="40" cx="50" cy="50"/>
                            <circle class="text-emerald-500" stroke-width="10" stroke-linecap="round" stroke="currentColor" fill="transparent" r="40" cx="50" cy="50"
                                    stroke-dasharray="251.2"
                                    stroke-dashoffset="{{ $dashOffset }}">
                            </circle>
                        </svg>
                        <p class="absolute inset-0 flex items-center justify-center text-lg font-semibold text-emerald-700">
                            {{ round($progressPercentage) }}%
                        </p>
                    </div>
                    <div class="bg-emerald-100 rounded-md p-3 md:p-4 mt-2">
                        <p class="text-emerald-700 text-sm">Completed Hours: <span class="font-semibold">{{ $completedHours }}</span> / {{ $totalHours }}</p>
                        <p class="text-emerald-700 text-sm mt-1">Remaining Hours: <span class="font-semibold">{{ $remainingHours }}</span></p>
                    </div>
                </div>

                <div>
                    <h4 class="text-lg font-semibold text-gray-700 mb-2"><i class="fas fa-info-circle mr-2"></i> Additional Information</h4>
                    <p class="text-gray-600 text-sm">More detailed information about the student or their progress could be displayed here. This could include reports, feedback logs, etc.</p>
                </div>
            @endif
        </div>
    </div>
</div>