@extends('layouts.unauth-layout')

@section('content')
<div id="loading-spinner" class="fixed inset-0 bg-white bg-opacity-90 flex items-center justify-center z-[9999]">
  <div class="text-emerald-500 text-4xl">
    <i class="fas fa-circle-notch fa-spin"></i>
  </div>
</div>

  <!-- Desktop Version -->
  <div class="w-full h-screen flex overflow-hidden relative" 
       x-data="{ 
          step: 1, 
          first_name: '', 
          last_name: '', 
          email: '', 
          password: '', 
          password_confirmation: '', 
          student_id: '', 
          course: '', 
          year_level: '', 
          school_id_image: null, 
          yearLevels: [], 
          isLeftPanelVisible: true,
          currentSlide: 1,
          totalSlides: 3,
          agreed: false,
          showTerms: false,
          canCloseTerms: false,
          showDevModal: false,
          loading: false,
          direction: 'right',
          countdownSeconds: 10,
          openTerms() {
              this.showTerms = true;
              this.canCloseTerms = false;
              this.countdownSeconds = 10;
              
              // Clear any existing interval
              if (this.countdownInterval) {
                clearInterval(this.countdownInterval);
              }
              
              this.countdownInterval = setInterval(() => {
                this.countdownSeconds -= 1;
                if (this.countdownSeconds <= 0) {
                  clearInterval(this.countdownInterval);
                  this.canCloseTerms = true;
                }
              }, 1000);
            },
          fetchYearLevels() { 
            if (this.course) { 
              fetch(`/get-year-levels?course_id=${this.course}`)
                .then(response => response.json())
                .then(data => { 
                  this.yearLevels = data; 
                }); 
            } else { 
              this.yearLevels = []; 
            } 
          },
          formatEmail() {
            if(this.email && !this.email.includes('@')) {
              this.email = this.email + '@gcc.com';
            } else if(this.email && !this.email.endsWith('@gcc.com')) {
              const username = this.email.split('@')[0];
              this.email = username + '@gcc.com';
            }
          },
          nextStep() {
            this.direction = 'right';
            if (this.step === 3) {
              this.isLeftPanelVisible = false;
            }
            this.step += 1;
          },
          prevStep() {
            this.direction = 'left';
            if (this.step === 4) {
              this.isLeftPanelVisible = true;
            }
            this.step -= 1;
          },
          submitForm() {
            if (!this.agreed) { 
              this.openTerms(); 
              return; 
            } 
            this.loading = true;
            setTimeout(() => {
              document.getElementById('registration-form').submit();
            }, 100);
          }
        }"
        x-init="
          setInterval(() => {
            currentSlide = currentSlide >= totalSlides ? 1 : currentSlide + 1;
          }, 5000);
        "
      >
        
      <!-- Left Panel - Desktop Only -->
      <div
        x-show="isLeftPanelVisible"
        x-transition:enter="transition ease-out duration-500"
        x-transition:enter-start="-translate-x-full opacity-0"
        x-transition:enter-end="translate-x-0 opacity-100"
        x-transition:leave="transition ease-in duration-500"
        x-transition:leave-start="translate-x-0 opacity-100"
        x-transition:leave-end="-translate-x-full opacity-0"
        class="hidden md:flex md:w-2/5 h-full text-white flex-col justify-center items-center px-10 relative transition-all duration-500 ease-in-out" 
        style="overflow: hidden;">
        
        <!-- Image Slider -->
        <template x-for="i in totalSlides" :key="i">
          <div 
            class="absolute top-0 left-0 w-full h-full z-0 transition-opacity duration-1000 ease-in-out"
            :class="currentSlide === i ? 'opacity-100' : 'opacity-0'"
            :style="`background: url('{{ asset('images/slide/o${i}.jpeg') }}') center center / cover no-repeat; filter: blur(3px);`">
          </div>
        </template>

        <!-- Overlay -->
        <div class="bg-black bg-opacity-30 w-full h-full absolute top-0 left-0 z-10"></div>
        
        <!-- Content -->
        <div class="z-20 relative text-center">
          <img src="{{ asset('images/logo.png') }}" alt="GCC Logo" class="w-40 h-40 mb-6 mx-auto">
          <h1 class="text-emerald-400 text-lg font-bold mb-6">GCC OJT MONITORING</h1>
          <h2 class="text-3xl font-semibold">This program is for Gingoog City Colleges Internship</h2>
          <a href="{{ url('/login') }}" class="relative inline-flex overflow-hidden rounded-full group border-2 border-white z-10 mt-6">
          <span class="relative z-10 flex items-center justify-center px-7 py-3 font-medium text-white transition-colors duration-300 group-hover:text-[#196b3a]">
              <span class="absolute inset-0 w-full h-full bg-white -translate-x-full -skew-x-12 origin-left transition-transform duration-500 ease-in-out group-hover:translate-x-0"></span>
              <span class="relative z-20">Sign in</span>
          </span>
      </a>
          <div class="mt-12 text-sm text-gray-300">
            <a href="#" @click.prevent="showDevModal = true" class="hover:underline">Developer</a>
          </div>
        </div>
      </div>

      <!-- Right Side - Desktop -->
      <div class="h-full bg-gray-100 flex flex-col justify-center items-center px-4 md:px-20 relative transition-all duration-500 ease-in-out w-full md:w-3/5"
     :class="step === 4 && isLeftPanelVisible === false ? 'md:w-full' : 'md:w-3/5'">
        @if ($activeSemester)
        <form id="registration-form" method="POST" action="{{ route('register.store') }}" enctype="multipart/form-data" class="w-full relative">
          @csrf
          
          <!-- Mobile Header (Hidden on Desktop) -->
          <div class="md:hidden text-center mb-8">
            <img src="{{ asset('images/logo.png') }}" alt="GCC Logo" class="w-24 h-24 mb-4 mx-auto">
            <h1 class="text-emerald-400 text-lg font-bold">GCC OJT MONITORING</h1>
            <p class="text-gray-600 mt-2">For Gingoog City Colleges Internship</p>
          </div>
          
          <!-- Steps Indicator for Mobile -->
          <div class="md:hidden w-full flex justify-center mb-6">
            <div class="flex items-center space-x-2">
              <template x-for="i in 4" :key="i">
                <div class="flex items-center">
                  <div 
                    class="w-8 h-8 rounded-full flex items-center justify-center transition-all duration-300"
                    :class="step === i ? 'bg-emerald-400 text-white' : 'bg-gray-200 text-gray-600'"
                  >
                    <span x-text="i"></span>
                  </div>
                  <div 
                    x-show="i < 4" 
                    class="w-6 h-1 transition-all duration-300"
                    :class="step > i ? 'bg-emerald-400' : 'bg-gray-200'"
                  ></div>
                </div>
              </template>
            </div>
          </div>
          <div class="relative h-[500px] md:h-auto overflow-hidden">

          <!-- Step 1: Personal Information -->
          <div x-show="step === 1" 
               x-transition:enter="transition ease-out duration-500 transform"
               x-transition:enter-start="opacity-0 translate-x-full"
               x-transition:enter-end="opacity-100 translate-x-0"
               x-transition:leave="transition ease-in duration-500 transform"
               x-transition:leave-start="opacity-100 translate-x-0"
               x-transition:leave-end="opacity-0 -translate-x-full"
               class="w-full absolute">
            <h2 class="text-2xl md:text-[2rem] text-gray-600 mb-4 text-start font-bold">Personal Information</h2>
            <h2 class="text-md text-gray-500 mb-6 text-start">Enter your personal information for student registration:</h2>
            
            <div class="flex flex-col md:flex-row gap-4">
              <input type="text" name="first_name" x-model="first_name" placeholder="First Name" class="w-full px-4 py-3 mb-2 md:mb-4 border border-gray-300 rounded-full bg-emerald-50 focus:ring-2 focus:ring-emerald-300 focus:outline-none">
              <input type="text" name="last_name" x-model="last_name" placeholder="Last Name" class="w-full px-4 py-3 mb-4 border border-gray-300 rounded-full bg-emerald-50 focus:ring-2 focus:ring-emerald-300 focus:outline-none">
            </div>
            
            <div class="relative mb-4">
              <div class="absolute left-4 top-[40%] transform -translate-y-1/2 flex items-center pointer-events-none">
                <i class="fas fa-envelope text-emerald-400"></i>
              </div>
              <input 
                type="text" 
                name="email" 
                x-model="email" 
                @blur="formatEmail()"
                placeholder="Enter Email" 
                class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-full bg-emerald-50 focus:ring-2 focus:ring-emerald-300 focus:outline-none">
              <p class="text-xs text-gray-500 mb-1">Email will be formatted as username@gcc.com
            </div>
            @error('email')<span class="text-red-400 mb-4 block">{{ $message }}</span>@enderror

            <button type="button" @click="nextStep()" class="w-full px-6 py-3 bg-emerald-400 text-white rounded-full hover:bg-emerald-500 transition transform active:scale-95">Next</button>
            
            <!-- Only visible on mobile -->
          <p class="mt-4 text-center text-gray-600 md:hidden">
            <a href="{{ url('/login') }}" class="hover:underline">I already have an account</a><br>
            <a href="#" @click.prevent="showDevModal = true" class="text-sm hover:underline">Developer</a>
          </p>
          </div>
          

          <!-- Step 2: Password Setup -->
        <div x-show="step === 2"
             x-transition:enter="transition ease-out duration-500 transform"
             x-transition:enter-start="opacity-0 translate-x-full" 
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-500 transform"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 -translate-x-full"
             class="w-full absolute">
            <h2 class="text-2xl md:text-[2rem] text-gray-600 mb-4 text-start font-bold">Set Your Password</h2>
            <h2 class="text-md text-gray-500 mb-6 text-start">Create a secure password for your account:</h2>

            <div class="relative mb-4">
              <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-emerald-400">
                <i class="fas fa-lock"></i>
              </div>
              <input 
                type="password" 
                name="password" 
                x-model="password" 
                placeholder="Enter Password" 
                class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-full bg-emerald-50 focus:ring-2 focus:ring-emerald-300 focus:outline-none">
              <button 
                type="button" 
                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500"
                @click="$el.previousElementSibling.type = $el.previousElementSibling.type === 'password' ? 'text' : 'password'">
                <i class="far fa-eye"></i>
              </button>
            </div>
            @error('password')<span class="text-red-400 mb-4 block">{{ $message }}</span>@enderror

            <div class="relative mb-4">
              <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-emerald-400">
                <i class="fas fa-lock"></i>
              </div>
              <input 
                type="password" 
                name="password_confirmation" 
                x-model="password_confirmation" 
                placeholder="Confirm Password" 
                class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-full bg-emerald-50 focus:ring-2 focus:ring-emerald-300 focus:outline-none">
              <button 
                type="button" 
                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500"
                @click="$el.previousElementSibling.type = $el.previousElementSibling.type === 'password' ? 'text' : 'password'">
                <i class="far fa-eye"></i>
              </button>
            </div>
            @error('password_confirmation')<span class="text-red-400 mb-4 block">{{ $message }}</span>@enderror

            <div class="flex justify-between">
              <button type="button" @click="prevStep()" class="px-6 py-3 border border-gray-300 rounded-full bg-white hover:bg-gray-200 transition transform active:scale-95">Back</button>
              <button type="button" @click="nextStep()" class="px-6 py-3 bg-emerald-400 text-white rounded-full hover:bg-emerald-500 transition transform active:scale-95">Next</button>
            </div>
          </div>

          <!-- Step 3: Student Details -->
        <div x-show="step === 3"
             x-transition:enter="transition ease-out duration-500 transform"
             x-transition:enter-start="opacity-0 translate-x-full" 
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-500 transform"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 -translate-x-full"
             class="w-full absolute">
               
            <h2 class="text-2xl md:text-[2rem] text-gray-600 mb-4 text-start font-bold">Student Details</h2>
            <h2 class="text-md text-gray-500 mb-6 text-start">Provide your student information:</h2>
          
            <div class="relative mb-4">
              <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-emerald-400">
                <i class="fas fa-id-card"></i>
              </div>
              <input 
                type="text" 
                name="student_id" 
                x-model="student_id" 
                placeholder="Student ID" 
                class="w-full pl-10 py-3 border border-gray-300 rounded-full bg-emerald-50 focus:ring-2 focus:ring-emerald-300 focus:outline-none">
            </div>
          
            <!-- Course Dropdown -->
            <div class="relative mb-4">
              <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-emerald-400">
                <i class="fas fa-graduation-cap"></i>
              </div>
              <select 
                name="course_id" 
                x-model="course" 
                @change="fetchYearLevels()" 
                class="w-full pl-10 py-3 border border-gray-300 rounded-full bg-emerald-50 focus:ring-2 focus:ring-emerald-300 focus:outline-none appearance-none">
                <option value="">Select Course</option>
                @if ($activeSemester)
                  @foreach ($activeSemester->courses as $course)
                    <option value="{{ $course->id }}">{{ $course->full_name }}</option>
                  @endforeach
                @endif
              </select>
              <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 pointer-events-none">
                <i class="fas fa-chevron-down"></i>
              </div>
            </div>
          
            <!-- Year Level Dropdown -->
            <div class="relative mb-4">
              <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-emerald-400">
                <i class="fas fa-layer-group"></i>
              </div>
              <select 
                name="year_level_id" 
                x-model="year_level"  
                :disabled="!course" 
                class="w-full pl-10 py-3 border border-gray-300 rounded-full bg-emerald-50 focus:ring-2 focus:ring-emerald-300 focus:outline-none appearance-none"
                :class="!course ? 'bg-gray-100' : ''">
                <option value="">Select Year Level</option>
                <template x-for="level in yearLevels" :key="level.id">
                  <option :value="level.id" x-text="level.level"></option>
                </template>
              </select>
              <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 pointer-events-none">
                <i class="fas fa-chevron-down"></i>
              </div>
            </div>

            <div class="flex justify-between">
              <button type="button" @click="prevStep()" class="px-6 py-3 border border-gray-300 rounded-full bg-white hover:bg-gray-200 transition transform active:scale-95">Back</button>
              <button type="button" @click="nextStep()" class="px-6 py-3 bg-emerald-400 text-white rounded-full hover:bg-emerald-500 transition transform active:scale-95">Next</button>
            </div>
          </div>

          <!-- Step 4: Upload ID (Optional) -->
          <div x-show="step === 4"
               x-transition:enter="transition ease-out duration-500 transform"
               x-transition:enter-start="opacity-0 translate-x-full" 
               x-transition:enter-end="opacity-100 translate-x-0"
               x-transition:leave="transition ease-in duration-500 transform"
               x-transition:leave-start="opacity-100 translate-x-0"
               x-transition:leave-end="opacity-0 -translate-x-full"
               class="w-full md:w-2/3 mx-auto transition-all duration-500 ease-in-out"
               x-data="{ previewUrl: null }">
            <h2 class="text-2xl md:text-[2rem] text-gray-600 mb-4 text-start font-bold">Upload ID Image</h2>
            <h2 class="text-md text-gray-500 mb-6 text-start">You can upload or capture your school ID image.</h2>
          
            <!-- Upload + Preview Container -->
            <div class="flex flex-col gap-4 mb-4">
              <label class="block relative w-full cursor-pointer group">
                <input 
                  type="file" 
                  name="school_id_image" 
                  accept="image/*" 
                  class="hidden" 
                  @change="const file = $event.target.files[0]; if (file) previewUrl = URL.createObjectURL(file);"
                >
          
                <div class="w-full h-64 border-2 border-dashed border-emerald-400 rounded-lg overflow-hidden relative bg-gradient-to-br from-emerald-100 to-emerald-200 transition">
                  <!-- Preview Image -->
                  <template x-if="previewUrl">
                    <img :src="previewUrl" alt="Preview" class="absolute inset-0 w-full h-full object-cover z-0">
                  </template>
          
                  <!-- Icon + Text -->
                  <div 
                    class="z-10 flex flex-col items-center justify-center h-full transition-all duration-300"
                    :class="{ 'opacity-0': previewUrl, 'opacity-100': !previewUrl }"
                  >
                    <div class="w-20 h-20 bg-emerald-400 rounded-full flex items-center justify-center shadow-md mb-4">
                      <!-- ID Icon -->
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" fill="white" viewBox="0 0 24 24" stroke="white">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 3a2 2 0 00-2 2v4h18V5a2 2 0 00-2-2H5zM21 11H3v8a2 2 0 002 2h14a2 2 0 002-2v-8zM7 15h4m-2-2v4" />
                      </svg>
                    </div>
                    <span class="text-emerald-700 font-semibold text-lg">Choose or Capture</span>
                  </div>
          
                  <!-- X icon to remove preview -->
                  <button 
                    type="button" 
                    class="absolute top-2 right-2 bg-white text-red-500 hover:bg-red-100 rounded-full p-1 shadow-md z-20"
                    x-show="previewUrl"
                    @click.stop="previewUrl = null; $event.preventDefault();"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                  </button>
                </div>
              </label>
          
              <!-- Use Camera Button (Mobile) -->
              <div class="md:hidden flex justify-center">
                <button
                  type="button"
                  @click="$refs.cameraInput.click()"
                  class="bg-emerald-100 text-emerald-700 px-4 py-2 rounded-full flex items-center justify-center gap-2 transform active:scale-95"
                >
                  <i class="fas fa-camera"></i>
                  <span>Use Camera</span>
                </button>
              </div>

              <!-- Hidden Camera Input Triggered by Click -->
              <input 
                type="file" 
                accept="image/*" 
                capture="environment" 
                name="school_id_image_camera" 
                x-ref="cameraInput" 
                class="hidden" 
                @change="const file = $event.target.files[0]; if (file) previewUrl = URL.createObjectURL(file);"
              >
            </div>
              
            <div class="mb-6 flex items-start space-x-2">
              <input type="checkbox" id="agree" x-model="agreed" class="mt-1 rounded-full">
              <label for="agree" class="text-gray-700 text-sm">
                I agree to the 
                <button type="button" class="text-emerald-600 underline" @click="openTerms()">Terms and Agreement</button> 
                regarding GPS tracking and usage.
              </label>
            </div>
          
            <div class="flex justify-between">
              <button type="button" @click="prevStep()" class="px-6 py-3 border border-gray-300 rounded-full bg-white hover:bg-gray-200 transition transform active:scale-95">Back</button>
              <button 
                type="button"  
                @click="submitForm()" 
                class="px-10 py-3 bg-emerald-400 text-white rounded-full hover:bg-emerald-500 transition relative transform active:scale-95 overflow-hidden"
              >
                <span 
                  class="button-text transition-all duration-300 ease-in-out relative z-10"
                  :class="{'opacity-0': loading, 'opacity-100': !loading}"
                >
                  Register
                </span>
                <span 
                  class="absolute inset-0 flex items-center justify-center gap-2 transition-all duration-300 ease-in-out z-20"
                  :class="{'opacity-100': loading, 'opacity-0 invisible': !loading}"
                >
                  <i class="fas fa-spinner fa-spin"></i> Registering...
                </span>
              </button>
            </div>
          </div>
          </div>
        </form>
        @else
          <div class="bg-white rounded-lg p-8 shadow-md text-center">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Registration Temporarily Unavailable</h2>
            <p class="text-gray-600">There is no active semester at the moment. Please check back later for registration.</p>
          </div>
        @endif
      </div>
      
    <div 
      x-show="showTerms" 
      x-cloak 
      x-transition:enter="transition ease-out duration-300"
      x-transition:enter-start="opacity-0"
      x-transition:enter-end="opacity-100"
      x-transition:leave="transition ease-in duration-300"
      x-transition:leave-start="opacity-100"
      x-transition:leave-end="opacity-0"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div 
        class="bg-white rounded-lg p-6 w-full max-w-lg m-4 relative" 
        @keydown.escape.window="canCloseTerms ? showTerms = false : null"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-300 transform"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
      >
        <!-- Icon at the top -->
        <div class="flex justify-center mb-4">
          <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center">
            <i class="fas fa-file-contract text-emerald-500 text-2xl"></i>
          </div>
        </div>
        
        <h2 class="text-xl font-bold mb-4 text-center">Terms and Agreement</h2>
        <p class="text-sm text-gray-600 mb-4">
          This system requires GPS tracking to monitor internship attendance. By registering, you agree that your location will be tracked for academic compliance and safety purposes.
        </p>
    
        <!-- Countdown timer -->
        <div 
          x-show="!canCloseTerms" 
          class="text-sm text-gray-400 text-center mb-4"
          x-text="`Please read the terms (${countdownSeconds}s)`"
          x-transition>
        </div>
    
        <!-- Close button -->
        <button 
          x-show="canCloseTerms"
          @click="showTerms = false; agreed = true" 
          class="w-full mt-4 px-4 py-2 bg-emerald-500 text-white rounded-full hover:bg-emerald-600 transition transform active:scale-95">
          I Understand
        </button>
      </div>
    </div>

    <!-- Developer Modal -->
<div 
  x-show="showDevModal" 
  x-cloak 
  x-transition:enter="transition ease-out duration-300"
  x-transition:enter-start="opacity-0"
  x-transition:enter-end="opacity-100"
  x-transition:leave="transition ease-in duration-300"
  x-transition:leave-start="opacity-100"
  x-transition:leave-end="opacity-0"
  class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div 
    class="bg-white rounded-lg p-6 w-full max-w-md m-4 relative"
    x-transition:enter="transition ease-out duration-300 transform"
    x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-300 transform"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
  >
    <!-- Developer icon -->
    <div class="flex justify-center mb-4">
      <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center modal-icon">
        <i class="fas fa-code text-emerald-600 text-2xl"></i>
      </div>
    </div>
    
    <h2 class="text-lg font-bold mb-2 text-center">System Developer</h2>
    <p class="text-sm text-gray-600 text-center">Developed by BSIT students of GCC. Contact your coordinator for technical issues.</p>
    <button @click="showDevModal = false" class="absolute top-4 right-4 text-gray-500 hover:text-black transform active:scale-95 transition">
      <i class="fas fa-times"></i>
    </button>
  </div>
</div>
</div>


  
    

<style>
  [x-cloak] { display: none !important; }
  
  /* Smooth width transitions */
  .transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 500ms;
  }
  
  /* Form container */
  #registration-form {
    max-width: 100%;
    transition: all 0.5s ease-in-out;
  }
  
  /* Steps container */
  #registration-form > .relative {
    min-height: 700px; /* Adjust based on your content */
  }
  
  /* Mobile specific styles */
  @media (max-width: 768px) {
    /* Add padding to steps */
    [x-show^="step"] {
      padding-right: 1rem;
      padding-left: 1rem;
    }
    
    /* Ensure form doesn't touch screen edges */
    .bg-gray-100 {
      padding-left: 0.5rem;
      padding-right: 0.5rem;
    }
  }
</style>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
    // Hide spinner when everything is loaded
    window.addEventListener('load', function() {
      document.getElementById('loading-spinner').style.display = 'none';
    });
    
    // Fallback in case load event doesn't fire
    setTimeout(function() {
      document.getElementById('loading-spinner').style.display = 'none';
    }, 1000);
  });
  
      document.addEventListener('DOMContentLoaded', function() {
        // Get email input field
        const emailInput = document.querySelector('input[name="email"]');
        
        if (emailInput) {
          // Format email when user leaves the field
          emailInput.addEventListener('blur', function() {
            formatEmail(this);
          });
          
          // Also format email when form is submitted
          const form = emailInput.closest('form');
          if (form) {
            form.addEventListener('submit', function() {
              formatEmail(emailInput);
            });
          }
        }
        
        function formatEmail(inputElement) {
          const value = inputElement.value.trim();
          
          if (value) {
            if (!value.includes('@')) {
              // No @ symbol, add @gcc.com
              inputElement.value = value + '@gcc.com';
            } else if (!value.endsWith('@gcc.com')) {
              // Has @ but not ending with gcc.com, replace domain
              const username = value.split('@')[0];
              inputElement.value = username + '@gcc.com';
            }
          }
        }
        
        
        const nextButtons = document.querySelectorAll('button[type="button"][class*="bg-emerald"]');
        nextButtons.forEach(button => {
          button.addEventListener('click', function() {
            // Add animation class
            this.classList.add('scale-95');
            
            // Remove animation class after transition
            setTimeout(() => {
              this.classList.remove('scale-95');
            }, 200);
          });
        });
        
        
        function handleResponsiveLayout() {
          const isMobile = window.innerWidth < 768;
          const formContainer = document.querySelector('form');
          
          if (formContainer) {
            if (isMobile) {
              formContainer.classList.add('mobile-view');
            } else {
              formContainer.classList.remove('mobile-view');
            }
          }
        }
        
        handleResponsiveLayout();
        window.addEventListener('resize', handleResponsiveLayout);
      });
    </script>
@endsection