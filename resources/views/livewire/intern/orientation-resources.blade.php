<div class="w-full bg-gradient-to-b from-white to-emerald-100 overflow-y-auto" x-data="{ modalOpen: false, modalContent: {} }">

    <!-- Section 1 -->
    <section class="py-20 w-full flex flex-col items-center justify-center px-4 space-y-6">
        <h1 class="text-4xl font-bold text-emerald-800 text-center">
            OJT Monitoring System
        </h1>
        <p class="text-center text-gray-600 max-w-xl">
            Welcome! This page guides you through the orientation before starting your internship.
        </p>
        <button class="bg-emerald-600 text-white px-6 py-2 rounded-full border border-emerald-700 shadow-md hover:bg-emerald-700 transition">
            Get Started
        </button>
        <div class="relative w-[80%] max-w-3xl h-60 mt-8 transform rotate-3 bg-white shadow-lg shadow-emerald-200 rounded-lg">
            <!-- Content for landscape div -->
        </div>
    </section>

    <!-- Section 2 -->
    <section class="py-20 w-full flex flex-col justify-center px-4">
        <div class="flex flex-col md:flex-row items-center md:items-start justify-center gap-8 w-full max-w-6xl mx-auto">
            <!-- Portrait div -->
            <div class="w-48 h-96 bg-white shadow-lg shadow-emerald-200 rounded-xl"></div>

            <!-- Text content -->
            <div class="text-left space-y-4">
                <h1 class="text-3xl font-bold text-emerald-800">
                    Track Your Internship
                </h1>
                <p class="text-gray-600 max-w-md">
                    Our system helps you monitor progress, submit reports, and stay on top of your OJT journey.
                </p>
                <button class="bg-emerald-600 text-white px-5 py-2 rounded-full border border-emerald-700 shadow-md hover:bg-emerald-700 transition">
                    Learn More
                </button>
            </div>
        </div>
    </section>

    <!-- Section 3: Resource Cards -->
    <!-- Section 3: Resource Cards -->
<section class="py-20 w-full px-4">
    <div class="max-w-6xl mx-auto">
        <h2 class="text-3xl font-bold text-emerald-800 text-center mb-10">Orientation Resources</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ([
                [
                    'icon' => 'fa-book-open',
                    'color' => 'text-blue-500',
                    'title' => 'Student Handbook',
                    'desc' => 'Learn about the guidelines, policies, and expectations throughout your internship. Understand your responsibilities and what the school expects from you while deployed in a partner agency.'
                ],
                [
                    'icon' => 'fa-pen-to-square',
                    'color' => 'text-pink-500',
                    'title' => 'Weekly Report Guide',
                    'desc' => 'Follow the correct structure and format for submitting your weekly logs. We’ve included sample entries, time tracking, and supervisor signature guidelines.'
                ],
                [
                    'icon' => 'fa-lightbulb',
                    'color' => 'text-yellow-500',
                    'title' => 'OJT Tips',
                    'desc' => 'Practical advice for maximizing your internship experience. From communication etiquette to task management and professional behavior.'
                ],
                [
                    'icon' => 'fa-circle-question',
                    'color' => 'text-purple-500',
                    'title' => 'FAQs',
                    'desc' => 'How does geofencing work? What if I can’t check-in? When are reports due? Find the most common questions answered here.'
                ],
                [
                    'icon' => 'fa-calendar-days',
                    'color' => 'text-green-500',
                    'title' => 'Schedule Overview',
                    'desc' => 'View how your working hours are tracked using the geofenced check-in system. See what days count, and how to make adjustments in special cases.'
                ],
                [
                    'icon' => 'fa-shield-halved',
                    'color' => 'text-red-500',
                    'title' => 'Privacy Policy',
                    'desc' => 'Learn how we use your location data securely through Leaflet maps and geofencing. Your data is protected and used only for attendance validation.'
                ],
            ] as $resource)
                <div 
                    class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition cursor-pointer"
                    @click="modalOpen = true; modalContent = {{ json_encode($resource) }}"
                >
                    <div class="text-4xl mb-4">
                        <i class="fas {{ $resource['icon'] }} {{ $resource['color'] }}"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-emerald-700 mb-2">{{ $resource['title'] }}</h3>
                    <p class="text-gray-600 text-sm">{{ $resource['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>


    <!-- Modal -->
    <div 
        class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50 transition duration-300"
        x-show="modalOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
    >
        <div 
            class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 relative"
            @click.away="modalOpen = false"
        >
            <button class="absolute top-3 right-3 text-gray-500 hover:text-red-500 text-xl" @click="modalOpen = false">
                <i class="fas fa-times"></i>
            </button>
            <div class="text-4xl mb-4 text-center">
                <i :class="'fas ' + modalContent.icon + ' ' + modalContent.color"></i>
            </div>
            <h3 class="text-2xl font-bold text-emerald-700 text-center" x-text="modalContent.title"></h3>
            <p class="text-gray-600 text-center mt-4" x-text="modalContent.desc"></p>
        </div>
    </div>

</div>
