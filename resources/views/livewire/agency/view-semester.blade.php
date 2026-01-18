<div x-data="{
    view: 'semester', // 'semester', 'course', or 'students'
    selectedSemester: null,
    selectedCourse: null,
    activeYearLevelFilter: null,
    filteredStudents() {
        if (!this.activeYearLevelFilter) {
            return this.$wire.students;
        }
        return this.$wire.students.filter(student => student.year_level?.level === this.activeYearLevelFilter);
    },
    init() {
        $wire.on('courses-loaded', () => {
            this.view = 'course';
        });
        $wire.on('students-loaded', () => {
            this.view = 'students';
            this.activeYearLevelFilter = null; // Reset filter when new students load
        });
    }
}" class="p-6 bg-white min-h-screen w-full">

    <nav class="mb-4 text-gray-600">
        <span x-show="view === 'semester'">Semester</span>
        <span x-show="view === 'course'">
            <button type="button" class="text-blue-600 hover:underline" @click="view = 'semester'; selectedSemester = null">Semester</button> >
            <span x-text="selectedSemester?.grading_description || ''"></span>
        </span>
        <span x-show="view === 'students'">
            <button type="button" class="text-blue-600 hover:underline" @click="view = 'course'; selectedCourse = null">Courses</button> >
            <span x-text="selectedCourse?.abbreviation || ''"></span> > Students
        </span>
    </nav>

    <h1 class="text-2xl font-bold">View Semester</h1>
    <p class="text-gray-500 text-sm">Manage students by semester and course.</p>

    <div x-show="view === 'semester'" x-transition>
        <table class="w-full bg-white shadow-md rounded-lg mt-4">
            <thead>
                <tr class="border-b">
                    <th class="p-3 text-left">Code</th>
                    <th class="p-3 text-left">Description</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($semesters as $semester)
                    <tr class="border-b">
                        <td class="p-3">{{ $semester->grading_code }}</td>
                        <td class="p-3">{{ $semester->grading_description }}</td>
                        <td class="p-3">{{ $semester->status }}</td>
                        <td class="p-3 text-right">
                            <button class="text-blue-600 hover:underline"
                                    wire:click="loadCourses({{ $semester->id }})"
                                    x-on:click="selectedSemester = {{ Js::from($semester) }}">
                                View Courses
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td class="p-3 text-center" colspan="4">No semesters found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div x-show="view === 'course'" x-transition>
        <button class="text-blue-600 hover:underline mb-4" @click="view = 'semester'; selectedSemester = null">← Back to Semesters</button>
        <h2 class="text-xl font-bold mb-4">Courses in <span x-text="selectedSemester?.grading_description || ''"></span></h2>
        <div class="bg-white shadow-md rounded-lg w-full">
            @forelse ($courses as $course)
                <div class="flex items-center justify-between p-4 border-b">
                    <div>
                        <p class="font-bold">{{ $course->abbreviation }}</p>
                        <p class="text-gray-500 text-sm">{{ $course->full_name }}</p>
                    </div>
                    <button class="bg-blue-500 text-white px-4 py-2 rounded-lg"
                            wire:click="loadStudents({{ $course->id }})"
                            x-on:click="selectedCourse = {{ Js::from($course) }}">
                        View Students
                    </button>
                </div>
            @empty
                <div class="p-4 text-center">No courses found for this semester.</div>
            @endforelse
        </div>
    </div>

    <div x-show="view === 'students'" x-transition>
        <button class="text-blue-600 hover:underline mb-4" @click="view = 'course'; selectedCourse = null">← Back to Courses</button>
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Students in <span x-text="selectedCourse?.abbreviation || ''"></span></h2>
        </div>

        <div class="mb-4">
            <button class="px-4 py-2 rounded-lg mr-2"
                    :class="{'bg-blue-500 text-white': activeYearLevelFilter === null, 'bg-gray-200 text-gray-700': activeYearLevelFilter !== null}"
                    @click="activeYearLevelFilter = null">All</button>
            <template x-for="yearLevel in $wire.yearLevels" :key="yearLevel.id">
                <button class="px-4 py-2 rounded-lg mr-2"
                        :class="{'bg-blue-500 text-white': activeYearLevelFilter === yearLevel.level, 'bg-gray-200 text-gray-700': activeYearLevelFilter !== yearLevel.level}"
                        @click="activeYearLevelFilter = yearLevel.level"
                        x-text="yearLevel.level"></button>
            </template>
        </div>

        <div class="bg-white shadow-md rounded-lg w-full">
            <template x-for="student in filteredStudents()" :key="student.id">
                <div class="flex items-center justify-between p-4 border-b">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gray-300 rounded-full overflow-hidden">
                            <img x-bind:src="student.profile_picture ? student.profile_picture : '/placeholder.jpg'" alt="Profile" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <p class="font-bold" x-text="student.name"></p>
                            <p class="text-gray-500 text-sm" x-text="student.year_level?.level"></p>
                        </div>
                    </div>
                    <span class="text-white px-3 py-1 rounded-lg bg-gray-500" x-text="student.course?.abbreviation"></span>
                </div>
            </template>
            <template x-if="filteredStudents().length === 0">
                <div class="p-4 text-center">No students found for the selected course and year level.</div>
            </template>
        </div>
    </div>
</div>