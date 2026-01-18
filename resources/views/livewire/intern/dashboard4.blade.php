<div x-data="{ show: false, tab: 'overview', mapVisible: @entangle('mapExposed') }" x-init="setTimeout(() => show = true, 200)" class="relative w-full h-screen bg-transparent flex flex-col items-center justify-end">
    
    <!-- Welcome -->
    <div class="absolute left-5 top-14 text-start h-[20vh]">
        <p class="text-white text-md opacity-100">Welcome</p>
        <h1 class="text-3xl font-bold text-white opacity-100">Good Morning, {{ Auth::user()->name }}!</h1>
    </div>

    <!-- Panel -->
    <div x-show="show"
         x-transition:enter="transform transition ease-in-out duration-500"
         x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
         class="absolute bottom-0 w-full bg-white rounded-t-3xl shadow-lg p-6 h-[80vh]">
        
        <!-- Tabs -->
        <div class="flex space-x-4 mb-4">
            <button @click="tab = 'overview'" :class="{ 'border-b-2 border-emerald-500 text-emerald-600': tab === 'overview' }" class="pb-2 text-gray-700 font-semibold">Overview</button>
            <button @click="tab = 'noticeboard'" :class="{ 'border-b-2 border-emerald-500 text-emerald-600': tab === 'noticeboard' }" class="pb-2 text-gray-700 font-semibold">Noticeboard</button>
        </div>

        <!-- Overview Tab -->
        <div x-show="tab === 'overview'" class="space-y-6">
            <!-- Date -->
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">Overview</h2>
                <div class="flex items-center border rounded-full px-3 py-1 bg-white shadow">
                    <i class="fas fa-calendar-alt text-emerald-500 mr-2"></i>
                    <span class="text-gray-700">{{ now()->format('D, F d Y') }}</span>
                </div>
                
                 <!-- Toggle -->
              <label for="mapToggle" class="flex flex-col items-center cursor-pointer select-none mt-6 text-dark dark:text-white">
                  Exposed in Map
                  <div class="relative">
                      <input
                          id="mapToggle"
                          type="checkbox"
                          class="peer sr-only"
                          x-model="mapVisible"
                          @change="$wire.call('toggleMapExposed', mapVisible)"
                      />
                      <div class="w-14 h-5 transition rounded-full bg-dark dark:bg-dark-2 peer-checked:bg-emerald-200 shadow-inner peer-checked:dark:bg-dark-3"></div>
                      <div class="absolute left-0 -top-1 h-7 w-7 bg-white dark:bg-dark-3 rounded-full shadow-switch-1 flex items-center justify-center transition peer-checked:translate-x-full peer-checked:bg-emerald-500 text-dark peer-checked:text-white">
                          <span class="w-4 h-4 border border-current rounded-full bg-inherit active"></span>
                      </div>
                  </div>
              </label>
            </div>

            <!-- Cards -->
            <div class="grid grid-cols-2 gap-4">
                <template x-for="(status, index) in ['Check In', 'Check Out', 'Break', 'Overtime']" :key="index">
                    <div class="bg-white p-6 rounded-xl border shadow-md flex flex-col relative h-40">
                        <i class="fas fa-clock text-emerald-500 absolute top-3 left-3" x-show="status == 'Check In'"></i>
                        <i class="fas fa-sign-out-alt text-red-500 absolute top-3 left-3" x-show="status == 'Check Out'"></i>
                        <i class="fas fa-coffee text-yellow-500 absolute top-3 left-3" x-show="status == 'Break'"></i>
                        <i class="fas fa-stopwatch text-blue-500 absolute top-3 left-3" x-show="status == 'Overtime'"></i>
                        <p class="text-center text-lg font-semibold text-gray-900" x-text="status"></p>
                        <i class="fas fa-ellipsis-v absolute top-3 right-3 text-gray-400 cursor-pointer"></i>
                        <div class="flex justify-between items-center mt-auto">
                            <span class="text-black font-bold text-lg">9:10 <span class="text-sm text-gray-500">AM</span></span>
                            <span class="bg-emerald-500 text-white text-xs px-3 py-1 rounded-full" x-show="status == 'Check In'">Online</span>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">Checked in success</p>
                    </div>
                </template>
            </div>

            <!-- Activity -->
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">Recent Activity</h2>
                <a href="#" class="text-emerald-600 font-medium">See All</a>
            </div>
            <div class="bg-white p-4 rounded-xl border shadow-md relative overflow-hidden">
                <i class="fas fa-check-circle text-emerald-500 absolute top-3 left-3"></i>
                <p class="text-lg font-semibold text-gray-900">Check In</p>
                <p class="text-sm text-gray-500">{{ now()->format('D, F d Y') }}</p>
                <span class="bg-emerald-500 text-white text-xs px-3 py-1 rounded-full absolute top-3 right-3">Online</span>
                <div class="absolute bottom-0 right-0 w-24 h-24 bg-emerald-200 rounded-full opacity-50"></div>
            </div>
        </div>

        <!-- Noticeboard Tab -->
        <div x-show="tab === 'noticeboard'" class="space-y-4">
            <livewire:noticeboard />
        </div>
    </div>
</div>
