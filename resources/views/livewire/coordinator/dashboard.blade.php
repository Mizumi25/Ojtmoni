<div x-data="{ tab: 'dashboard' }" class="p-4 md:p-6 lg:p-8 min-h-screen w-full overflow-hidden">
    <!-- Tabs -->
    <div class="mb-4 md:mb-6 flex space-x-4">
        <button 
            @click="tab = 'dashboard'" 
            :class="tab === 'dashboard' ? 'border-emerald-600 text-emerald-600' : 'text-gray-500'" 
            class="pb-2 border-b-2 font-semibold focus:outline-none text-sm md:text-base">
            Dashboard
        </button>
        <button 
            @click="tab = 'notice'" 
            :class="tab === 'notice' ? 'border-emerald-600 text-emerald-600' : 'text-gray-500'" 
            class="pb-2 border-b-2 font-semibold focus:outline-none text-sm md:text-base">
            Notice Board
        </button>
    </div>
   <div x-show="tab === 'dashboard'" x-transition>
        <div class="max-w-full mx-auto space-y-6 md:space-y-8 lg:space-y-12">
            <!-- TOP SECTION (Full Width, Increased Height) -->
            <div class="max-w-full bg-white rounded-2xl shadow-lg p-4 md:p-6 flex flex-col md:flex-row justify-between gap-4 md:gap-6 h-auto md:h-[350px]">
                
                <!-- Left Panel (Text, Tabs, and Button) -->
                <div class="flex flex-col justify-between w-full md:w-1/3 mb-4 md:mb-0">
                    <div>
                        <h2 class="text-lg md:text-xl font-bold text-gray-800">OJT Overview</h2>
    
                        <!-- Modern Tabs (No Gray Line) -->
                        <div class="flex space-x-4 md:space-x-6 mt-3 md:mt-4">
                            <span class="text-emerald-600 font-semibold border-b-2 border-emerald-600 pb-2 cursor-pointer text-sm md:text-base">Active</span>
                            <span class="text-gray-500 pb-2 cursor-pointer hover:text-emerald-600 text-sm md:text-base">Pending</span>
                        </div>
                    </div>
    
                    <!-- Button at Bottom Right -->
                    <button class="mt-4 md:mt-6 py-2 md:py-3 bg-emerald-500 text-white rounded-full shadow-md hover:bg-emerald-600 w-full md:w-1/2 self-center md:self-end text-sm md:text-base">
                        Manage OJT
                    </button>
                </div>
    
                <!-- Right Panel (Scrollable Cards) -->
                <div class="flex space-x-3 md:space-x-4 w-full md:w-2/3 overflow-x-auto scrollbar-hide pb-2">
                  @php
                      $cardData = [
                          [
                              'color1' => 'from-blue-200',
                              'color2' => 'to-pink-200',
                              'base' => 'bg-blue-400',
                              'icon' => 'user-graduate',
                              'title' => 'Total OJT Students',
                              'count' => $users->where('role', 'student')->count(),
                              'sub' => 'Enrolled',
                          ],
                          [
                              'color1' => 'from-red-200',
                              'color2' => 'to-orange-200',
                              'base' => 'bg-red-400',
                              'icon' => 'briefcase',
                              'title' => 'Ongoing Internships',
                              'count' => $users->where('role', 'intern')->where('status', 'ongoing')->count(),
                              'sub' => 'Currently in Training',
                          ],
                          [
                              'color1' => 'from-yellow-200',
                              'color2' => 'to-orange-200',
                              'base' => 'bg-yellow-400',
                              'icon' => 'hourglass-half',
                              'title' => 'Pending Approvals',
                              'count' => $users->where('status', 'pending')->count(),
                              'sub' => 'Awaiting Confirmation',
                          ],
                          [
                              'color1' => 'from-green-200',
                              'color2' => 'to-yellow-200',
                              'base' => 'bg-green-400',
                              'icon' => 'check-circle',
                              'title' => 'Completed Internships',
                              'count' => $users->where('role', 'intern')->where('status', 'completed')->count(),
                              'sub' => 'Successfully Finished',
                          ],
                      ];
                  @endphp
              
                  @foreach ($cardData as $card)
                      <div class="min-w-[150px] sm:min-w-[160px] md:min-w-[180px] bg-gradient-to-b {{ $card['color1'] }} {{ $card['color2'] }} rounded-2xl shadow-lg p-3 md:p-4 flex flex-col justify-between flex-shrink-0">
                          <div class="flex flex-col items-start">
                              <div class="w-10 h-10 md:w-12 md:h-12 {{ $card['base'] }} rounded-full flex items-center justify-center">
                                  <i class="fas fa-{{ $card['icon'] }} text-lg md:text-xl text-white"></i>
                              </div>
                              <p class="text-white text-xs md:text-sm font-semibold mt-2 text-left">{{ $card['title'] }}</p>
                          </div>
                          <div class="text-left">
                              <p class="text-black font-bold text-lg md:text-xl">{{ $card['count'] }}</p>
                              <p class="text-gray-600 text-xs md:text-sm">{{ $card['sub'] }}</p>
                          </div>
                      </div>
                  @endforeach
              </div>
            </div>
        
    
    
            <!-- BOTTOM SECTION -->
            <div class="w-full flex flex-col md:flex-row space-y-6 md:space-y-0 md:space-x-6">
                <!-- Left Panel: List with Tabs (Fixed Height, Scrollable) -->
                <div class="flex-1">
                    <div class="flex space-x-4 md:space-x-6">
                        <span class="text-emerald-600 font-semibold border-b-2 border-emerald-600 pb-2 cursor-pointer text-sm md:text-base">Recent</span>
                        <span class="text-gray-500 pb-2 cursor-pointer hover:text-emerald-600 text-sm md:text-base">All Students</span>
                    </div>
    
                    <!-- List (Fixed Height, Scrollable) -->
                    <div class="mt-3 md:mt-4 space-y-3 md:space-y-4 h-[300px] md:h-[400px] overflow-y-auto scrollbar-hide">
                        @foreach ($users as $ojt)
                          <div class="flex items-center justify-between border-b pb-3 group hover:bg-emerald-50 p-2 rounded-lg">
                              <div class="flex items-center space-x-2 md:space-x-4">
                                  <div class="w-10 h-10 md:w-12 md:h-12 bg-gray-200 rounded-full"></div>
                                  <div>
                                      <p class="text-gray-800 font-semibold text-sm md:text-base">{{ $ojt->name }}</p>
                                      <p class="text-gray-500 text-xs md:text-sm">{{ $ojt->agency->agency_name ?? 'Unknown' }}</p>
                                  </div>
                              </div>
                              <div class="flex items-center space-x-1 md:space-x-2">
                                  <span class="px-2 md:px-3 py-1 text-xs md:text-sm rounded-full 
                                      {{ $ojt->status == 'active' ? 'bg-green-100 text-green-600' : 
                                         ($ojt->status == 'pending' ? 'bg-yellow-100 text-yellow-600' :
                                         ($ojt->status == 'completed' ? 'bg-blue-100 text-blue-600' :
                                         ($ojt->status == 'approved' ? 'bg-teal-100 text-teal-600' :
                                         ($ojt->status == 'intern' ? 'bg-purple-100 text-purple-600' :
                                         ($ojt->status == 'rejected' ? 'bg-red-100 text-red-600' :
                                         ($ojt->status == 'incomplete' ? 'bg-orange-100 text-orange-600' : 'bg-gray-100 text-gray-600'))))))}}">
                                      {{ $ojt->status }}
                                  </span>
                                  <div class="relative">
                                      <div class="w-6 h-6 flex items-center justify-center text-gray-500 cursor-pointer group-hover:text-white group-hover:bg-emerald-500 rounded-full">
                                          ...
                                      </div>
                                  </div>
                              </div>
                          </div>
                      @endforeach
    
                    </div>
                </div>
    
                <!-- Right Panel: GPS Map (Increased Height) -->
                <div class="w-full md:w-1/3 bg-white rounded-2xl shadow-lg p-4 md:p-6 flex flex-col justify-between h-[300px] md:h-[400px]">
                    <p class="text-gray-600 text-sm md:text-base">OJT Location Tracking</p>
                    <div class="flex-1 flex items-center justify-center rounded-lg bg-gray-200 mt-3 md:mt-4">
                      <div class="flex-1 h-[200px] md:h-[250px] relative" x-data="{
                        map: null,
                    }" x-init="
                        map = L.map('map').setView([8.8278, 125.0853], 13);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
                        this.map = map;
                    ">
                        <div id="map" class="w-full rounded-lg" style="height: 100%; z-index: 10;"></div>
                    </div>
                    </div>
                    <button class="mt-3 md:mt-4 py-2 md:py-3 bg-emerald-500 text-white rounded-full shadow-md hover:bg-emerald-600 flex justify-between px-4 text-sm md:text-base">
                        View <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
    
        </div>
    </div>
    
    <div x-show="tab === 'notice'" x-transition>
      <div class="max-w-full mx-auto space-y-6 md:space-y-8 lg:space-y-12">
          <livewire:noticeboard />
      </div>
    </div>
</div>