<div class="min-h-screen w-full overflow-y-auto bg-gray-50" x-data="{
    stats: [
        { label: 'Total Students', value: {{ $totalStudents }}, progress: {{ $totalStudents > 0 ? round(($totalStudents / max($totalStudents, 1)) * 100) : 0 }}, icon: 'fas fa-user-graduate text-blue-400', bg: 'bg-blue-100', barColor: 'bg-blue-200' },
        { label: 'Completed OJT', value: {{ $completedStudents }}, progress: {{ $totalStudents > 0 ? round(($completedStudents / max($totalStudents, 1)) * 100) : 0 }}, icon: 'fas fa-check-circle text-green-400', bg: 'bg-green-100', barColor: 'bg-green-200' },
        { label: 'Total Daily Logs', value: {{ $totalDailyLogs }}, progress: {{ $totalDailyLogs > 0 ? round(($completedLogs / max($totalDailyLogs, 1)) * 100) : 0 }}, icon: 'fas fa-clipboard-list text-purple-400', bg: 'bg-purple-100', barColor: 'bg-purple-200' },
        { label: 'Completion Rate', value: '{{ $totalStudents > 0 ? round(($completedStudents / $totalStudents) * 100) : 0 }}%', progress: {{ $totalStudents > 0 ? round(($completedStudents / $totalStudents) * 100) : 0 }}, icon: 'fas fa-chart-line text-red-400', bg: 'bg-red-100', barColor: 'bg-red-200' }
    ],
    students: [
        @foreach($recentStudents ?? [] as $index => $student)
            { 
                name: '{{ $student->name }}', 
                status: '{{ $student->status }}', 
                hours: {{ $student->hours ?? 0 }}
            }{{ !$loop->last ? ',' : '' }}
        @endforeach
    ],
    cards: [
        { title: 'Student Management', icon: 'fas fa-user-graduate', desc: 'Manage student records', bg: 'bg-blue-200', circle: 'bg-blue-300', route: '{{ route("record.student") }}' },
        { title: 'Agency Management', icon: 'fas fa-building', desc: 'Manage partner agencies', bg: 'bg-green-200', circle: 'bg-green-300', route: '{{ route("record.company") }}' },
        { title: 'Coordinator Management', icon: 'fas fa-users-cog', desc: 'Manage coordinators', bg: 'bg-purple-200', circle: 'bg-purple-300', route: '{{ route("record.coordinator") }}' },
        { title: 'Course & Semester', icon: 'fas fa-calendar-alt', desc: 'Manage academic periods', bg: 'bg-red-200', circle: 'bg-red-300', route: '{{ route("record.coursesem") }}' }
    ],
    tab: 'dashboard'
}">
    <div class="p-4 md:p-6 space-y-6">
        <!-- Header with modern semester info -->
        <div class="flex justify-between items-center">
            <h1 class="text-xl md:text-2xl font-semibold text-gray-800">Admin Dashboard</h1>
            @if($activeSemester)
                <div class="text-gray-600 text-sm font-medium flex items-center space-x-1">
                    <span class="text-emerald-500">
                        <i class="fas fa-calendar-check"></i>
                    </span>
                    <span>Active: {{ $activeSemester->grading_description }}</span>
                </div>
            @endif
        </div>

        <!-- Tabs -->
        <div class="mb-6 flex space-x-4 overflow-x-auto pb-1">
            <button 
                @click="tab = 'dashboard'" 
                :class="tab === 'dashboard' ? 'border-emerald-600 text-emerald-600' : 'text-gray-500'" 
                class="pb-2 border-b-2 font-semibold focus:outline-none whitespace-nowrap">
                Dashboard
            </button>
            <button 
                @click="tab = 'notice'" 
                :class="tab === 'notice' ? 'border-emerald-600 text-emerald-600' : 'text-gray-500'" 
                class="pb-2 border-b-2 font-semibold focus:outline-none whitespace-nowrap">
                Notice Board
            </button>
        </div>

        <!-- Dashboard Content -->
        <div x-show="tab === 'dashboard'" x-transition class="space-y-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <template x-for="(item, index) in stats" :key="index">
                    <div class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition-shadow duration-300 flex flex-col h-40 md:h-48"> 
                        <div class="flex justify-between items-start">
                            <div>
                                <h2 class="text-xl md:text-2xl font-bold text-gray-900" x-text="item.value"></h2>
                                <p class="text-gray-600 text-sm" x-text="item.label"></p>
                            </div>
                            <div class="p-2 rounded-md" :class="item.bg">
                                <i :class="item.icon" class="text-lg"></i>
                            </div>
                        </div>
                        <div class="mt-auto">
                            <p class="text-gray-500 text-xs" x-text="item.progress + '% Compliance'"></p>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                <div class="h-2 rounded-full" :class="item.barColor" :style="'width: ' + item.progress + '%'"></div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Graph and Student List Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Graph Chart -->
                <div class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition-shadow duration-300">
                    <h2 class="text-lg font-semibold text-gray-700">OJT Status Distribution</h2>
                    <div class="w-full h-48 flex items-end space-x-1 md:space-x-3 mt-4">
                        <div class="flex-1 bg-sky-400 transition-all rounded-t-sm" style="height: {{ ($pendingStudents / max($totalStudents, 1)) * 100 }}%">
                            <p class="text-xs text-center text-white font-bold">{{ $pendingStudents }} ({{ round(($pendingStudents / max($totalStudents, 1)) * 100) }}%)</p>
                        </div>
                        <div class="flex-1 bg-green-500 transition-all rounded-t-sm" style="height: {{ ($approvedStudents / max($totalStudents, 1)) * 100 }}%">
                            <p class="text-xs text-center text-white font-bold">{{ $approvedStudents }} ({{ round(($approvedStudents / max($totalStudents, 1)) * 100) }}%)</p>
                        </div>
                        <div class="flex-1 bg-purple-500 transition-all rounded-t-sm" style="height: {{ ($internStudents / max($totalStudents, 1)) * 100 }}%">
                            <p class="text-xs text-center text-white font-bold">{{ $internStudents }} ({{ round(($internStudents / max($totalStudents, 1)) * 100) }}%)</p>
                        </div>
                        <div class="flex-1 bg-emerald-500 transition-all rounded-t-sm" style="height: {{ ($completedStudents / max($totalStudents, 1)) * 100 }}%">
                            <p class="text-xs text-center text-white font-bold">{{ $completedStudents }} ({{ round(($completedStudents / max($totalStudents, 1)) * 100) }}%)</p>
                        </div>
                    </div>
                    <div class="flex justify-between text-xs md:text-sm text-gray-600 mt-2">
                        <span>Pending</span>
                        <span>Approved</span>
                        <span>Intern</span>
                        <span>Completed</span>
                    </div>
                </div>

                <!-- Student List with Modern Status Colors -->
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow duration-300">
                    <div class="flex justify-between items-center p-4 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-700">Recent Students</h2>
                        <a href="{{ route('record.student') }}" class="text-emerald-600 text-sm hover:underline">See All</a>
                    </div>

                    <div class="overflow-y-auto max-h-64">
                        <div class="sticky top-0 bg-gray-50 p-3 flex justify-between text-gray-700 font-semibold text-sm border-b border-gray-200">
                            <span class="w-1/2">Name</span>
                            <span class="w-1/4 text-center">Status</span>
                            <span class="w-1/4 text-right">Hours</span>
                        </div>
                        <div class="divide-y divide-gray-100">
                            <template x-for="(student, index) in students" :key="index">
                                <div class="p-3 flex justify-between text-gray-600 hover:bg-gray-50">
                                    <span class="w-1/2" x-text="student.name"></span>
                                    <span class="w-1/4 text-center font-medium" 
                                          :class="{
                                              'text-green-500 bg-green-50 px-2 py-1 rounded-full text-xs': student.status === 'approved',
                                              'text-sky-500 bg-sky-50 px-2 py-1 rounded-full text-xs': student.status === 'pending',
                                              'text-purple-500 bg-purple-50 px-2 py-1 rounded-full text-xs': student.status === 'intern',
                                              'text-emerald-500 bg-emerald-50 px-2 py-1 rounded-full text-xs': student.status === 'completed',
                                              'text-red-500 bg-red-50 px-2 py-1 rounded-full text-xs': student.status === 'rejected',
                                              'text-amber-500 bg-amber-50 px-2 py-1 rounded-full text-xs': student.status === 'incomplete'
                                          }" 
                                          x-text="student.status.charAt(0).toUpperCase() + student.status.slice(1)">
                                    </span>
                                    <span class="w-1/4 text-right" x-text="student.hours"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Access Cards -->
            <div>
                <h2 class="text-sm font-semibold text-gray-700 mb-3">Quick Access</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <template x-for="(card, index) in cards" :key="index">
                        <a :href="card.route" :class="card.bg" class="p-4 rounded-lg flex items-center shadow hover:shadow-lg transition-shadow duration-300 cursor-pointer">
                            <div :class="card.circle" class="w-10 h-10 rounded-full flex items-center justify-center text-white">
                                <i :class="card.icon"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <h3 class="text-gray-800 text-sm font-semibold" x-text="card.title"></h3>
                                <p class="text-gray-700 text-xs" x-text="card.desc"></p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-600"></i>
                        </a>
                    </template>
                </div>
            </div>
        </div>

        <!-- Notice Board Content -->
        <div x-show="tab === 'notice'" x-transition class="space-y-6">
            <div class="max-w-full mx-auto space-y-6">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Notice Board</h2>
                    <div class="border-l-4 border-red-500 pl-4 py-2 mb-6">
                        <livewire:noticeboard />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>