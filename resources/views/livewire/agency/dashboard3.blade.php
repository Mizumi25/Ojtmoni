
    <div class="p-6 bg-white min-h-screen w-full overflow-auto space-y-10">
    <!-- Page Title -->
    <div>
        <h1 class="text-3xl font-bold">Dashboard</h1>
        <p class="text-gray-500">Overview of agency analytics and performance.</p>
    </div>
    
<!-- Main Panels -->
<div class="grid grid-cols-3 gap-6">
    <!-- First Panel (Bigger) -->
    <div class="space-y-4 col-span-2">
        <!-- Top Emerald Box -->
        <div class="bg-emerald-100 rounded-lg p-4 flex justify-between items-center">
            <h2 class="text-emerald-600 font-bold text-lg">Agency Overview</h2>
            <button class="mt-4 bg-emerald-500 text-white px-4 py-2 rounded-lg float-right">Details</button>
        </div>
        <!-- Curly Line Graph -->
        <div class="bg-white rounded-lg p-4 shadow">
            <h2 class="font-bold">Performance Trends</h2>
            <button class="border border-gray-300 text-gray-600 px-3 py-1 rounded-full float-right">Filter</button>
            <div class="h-32 w-full mt-4 bg-gray-200 flex items-center justify-center rounded-lg">Graph Placeholder</div>
        </div>
    </div>

    <!-- Second Panel (Smaller) -->
    <div class="space-y-6">
        <!-- Statistic Cards -->
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-transparent rounded-lg p-4">
                <i class="fas fa-users text-blue-400 bg-blue-100 p-3 rounded-lg"></i>
                <p class="text-gray-500">Total Students</p>
                <h2 class="text-2xl font-bold">150</h2>
                <div class="w-full bg-gray-200 h-2 rounded-full mt-2">
                    <div class="bg-blue-500 h-2 rounded-full" style="width: 80%;"></div>
                </div>
            </div>
            <div class="bg-transparent rounded-lg p-4">
                <i class="fas fa-clock text-orange-400 bg-orange-100 p-3 rounded-lg"></i>
                <p class="text-gray-500">On-Time Attendance</p>
                <h2 class="text-2xl font-bold">95%</h2>
                <div class="w-full bg-gray-200 h-2 rounded-full mt-2">
                    <div class="bg-orange-500 h-2 rounded-full" style="width: 90%;"></div>
                </div>
            </div>
            <div class="bg-transparent rounded-lg p-4">
                <i class="fas fa-check-circle text-green-400 bg-green-100 p-3 rounded-lg"></i>
                <p class="text-gray-500">Compliance Rate</p>
                <h2 class="text-2xl font-bold">92%</h2>
                <div class="w-full bg-gray-200 h-2 rounded-full mt-2">
                    <div class="bg-green-500 h-2 rounded-full" style="width: 92%;"></div>
                </div>
            </div>
            <div class="bg-transparent rounded-lg p-4">
                <i class="fas fa-map-marker-alt text-red-400 bg-red-100 p-3 rounded-lg"></i>
                <p class="text-gray-500">GPS Tracking</p>
                <h2 class="text-2xl font-bold">Active</h2>
                <div class="w-full bg-gray-200 h-2 rounded-full mt-2">
                    <div class="bg-red-500 h-2 rounded-full" style="width: 100%;"></div>
                </div>
            </div>
        </div>
        <!-- Bar Chart Section -->
        <div class="bg-white rounded-lg p-4 shadow">
            <p class="text-gray-500">Overall Engagement</p>
            <h2 class="font-bold text-xl">85%</h2>
            <span class="text-gray-500 text-sm flex items-center">+5% <i class="fas fa-arrow-up text-emerald-500 ml-1"></i></span>
            <div class="h-32 w-full mt-4 bg-gray-200 flex items-center justify-center rounded-lg">Bar Chart Placeholder</div>
        </div>
    </div>
</div>

  <!-- Bottom Wide Cards -->
    <div class="grid grid-cols-3 gap-4 w-full">
        <div class="bg-gray-200 p-6 rounded-lg flex items-center justify-between">
            <div class="p-4 bg-emerald-500 text-white rounded-lg"><i class="fas fa-user-graduate"></i></div>
            <div>
                <h2 class="font-bold">Student Management</h2>
                <p class="text-gray-500 text-sm">Manage student records</p>
                <button class="mt-2 bg-emerald-500 text-white px-4 py-2 rounded-lg">View</button>
            </div>
        </div>
        <div class="bg-gray-200 p-6 rounded-lg flex items-center justify-between">
            <div class="p-4 bg-emerald-500 text-white rounded-lg"><i class="fas fa-file-alt"></i></div>
            <div>
                <h2 class="font-bold">Reports & Compliance</h2>
                <p class="text-gray-500 text-sm">Analyze performance</p>
                <button class="mt-2 bg-emerald-500 text-white px-4 py-2 rounded-lg">View</button>
            </div>
        </div>
        <div class="bg-gray-200 p-6 rounded-lg flex items-center justify-between">
            <div class="p-4 bg-emerald-500 text-white rounded-lg"><i class="fas fa-map"></i></div>
            <div>
                <h2 class="font-bold">GPS Tracking</h2>
                <p class="text-gray-500 text-sm">Monitor locations</p>
                <button class="mt-2 bg-emerald-500 text-white px-4 py-2 rounded-lg">View</button>
            </div>
        </div>
    </div>
</div>

