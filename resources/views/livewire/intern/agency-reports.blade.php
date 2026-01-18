<div class="relative w-full h-screen bg-transparent flex flex-col">
    <!-- Profile Section (Outside Animated Panel) -->
    <div class="w-full h-[25vh] flex flex-col items-center justify-center bg-transparent mt-10">
        <div class="w-24 h-24 bg-gray-400 rounded-full"></div>
        <h2 class="text-white font-semibold text-lg mt-2">Company Name</h2>
        <p class="text-gray-300 text-sm">company@email.com</p>
    </div>

    <!-- Animated Panel -->
    <div x-data="{ show: false }" x-init="setTimeout(() => show = true, 200)" x-show="show"
         x-transition:enter="transform transition ease-in-out duration-500"
         x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
         class="absolute bottom-0 w-full bg-white rounded-t-[2rem] shadow-lg p-6 h-[70vh] flex flex-col gap-4 overflow-y-auto">
        
        <!-- Full-width Emerald Section -->
        <div class="w-full bg-emerald-900 text-white p-4 rounded-lg flex justify-between items-center">
            <div class="flex items-center gap-2">
                <div class="bg-emerald-600 p-2 rounded-full">
                    <i class="fas fa-star text-white text-lg"></i>
                </div>
                <div>
                    <p class="text-sm">Ratings</p>
                    <p class="font-bold text-xl">4.8</p>
                </div>
            </div>
            <span class="text-gray-300">|</span>
            <div class="flex items-center gap-2">
                <div class="bg-emerald-600 p-2 rounded-full">
                    <i class="fas fa-comment text-white text-lg"></i>
                </div>
                <div>
                    <p class="text-sm">Feedbacks</p>
                    <p class="font-bold text-xl">125</p>
                </div>
            </div>
        </div>

        <!-- Comments/Feedback Section -->
        <div class="bg-white shadow-md rounded-lg p-4">
            <div class="flex justify-between items-center mb-2">
                <h3 class="font-semibold">Comments/Feedback</h3>
                <span class="text-gray-500 text-sm">Date: 2023-10-01</span>
            </div>
            <p class="text-gray-700">This is a sample feedback comment. It provides insights about the service.</p>
            <p class="text-gray-700 mt-2">Another feedback comment can go here.</p>
        </div>

        <!-- Additional content (e.g., reviews) can go here -->
    </div>
</div>