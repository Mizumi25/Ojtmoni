@extends('layouts.unauth-layout')

@section('content')
<div id="loading-spinner" class="fixed inset-0 bg-white bg-opacity-90 flex items-center justify-center z-[9999]">
  <div class="text-emerald-500 text-4xl">
    <i class="fas fa-circle-notch fa-spin"></i>
  </div>
</div>

  <!-- Desktop Version - Unchanged -->
  <div class="hidden sm:block w-[800px] h-[550px] bg-[#258967] absolute z-0 top-[50%] left-[50%] transform translate-x-[-50%] translate-y-[-50%] rounded-2xl"></div>
  <form method="POST" action="{{ route('login.store') }}" id="login-form-desktop" class="hidden sm:block bg-[#ffffff] shadow-2xl p-[100px] w-[800px] h-[550px] rounded-2xl relative rounded-tr-[200px] z-10">
    @csrf
    <!-- Desktop content remains unchanged -->
    <div class="absolute right-0 top-[20%] w-[50%] flex items-center justify-center flex-col gap-6 px-6">
      <!-- Email/Student ID Field -->
      <div class="flex flex-col gap-[4px] w-[85%]">
        <label for="login-input">Email or Student ID</label>
        <div class="relative w-full">
          <div class="absolute left-6 top-1/2 transform -translate-y-1/2 text-[#0eae83] pointer-events-none z-10 flex items-center justify-center">
            <i class="fas fa-envelope"></i>
          </div>
          <input type="text" id="login-input" name="login"
          class="bg-[#d0efe7] pl-16 pr-8 py-[15px] rounded-full w-full" placeholder="Email or Student ID...">
        </div>
        @error('login')
          <span class="text-red-400 mt-1">{{ $message }}</span>
        @enderror
      </div>
      
      <!-- Password Field with Toggle -->
      <div class="flex flex-col gap-[4px] w-[85%]">
        <label for="password-input">Password</label>
        <div class="relative w-full">
          <div class="absolute left-6 top-1/2 transform -translate-y-1/2 text-[#0eae83] pointer-events-none z-10 flex items-center justify-center">
            <i class="fas fa-lock"></i>
          </div>
          <input type="password" id="password-input" name="password"
                 class="bg-[#d0efe7] pl-16 pr-16 py-[15px] rounded-full w-full" 
                 placeholder="Password...">
          <button type="button" class="password-toggle absolute right-6 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-[#0eae83] focus:outline-none z-10 flex items-center justify-center">
            <i class="far fa-eye"></i>
          </button>
        </div>
        @error('password')
          <span class="text-red-400 mt-1">{{ $message }}</span>
        @enderror
      </div>
      
      <div class="flex gap-2 text-start w-[85%] pl-4">
        <input type="checkbox" name="remember" id="remember">
        <label for="remember" class="text-gray-500">Remember me</label>
      </div>
      
      <div class="w-[85%]">
        <button type="submit" 
            class="w-full bg-[#0eae83] px-10 h-[60px] rounded-full text-white relative 
                   transition-all duration-200 ease-in-out active:scale-[0.95] transform
                   hover:bg-[#0d9e76] focus:outline-none focus:ring-2 focus:ring-[#0eae83] focus:ring-opacity-50
                   disabled:opacity-80 disabled:cursor-not-allowed login-button">
            <span class="button-text transition-all duration-300 ease-in-out relative z-10">Login</span>
            <span class="button-loading absolute inset-0 flex items-center justify-center gap-2 
                        transition-all duration-300 ease-in-out opacity-0 invisible z-20">
              <i class="fas fa-spinner fa-spin"></i> Logging in...
            </span>
          </button>
      </div>
    </div>
    <div id="right-form" class="bg-gradient-to-tr from-emerald-700 to-emerald-500 absolute left-0 top-0 p-5 h-full rounded-tr-[160px] rounded-bl-[160px] flex flex-col justify-center items-center w-[50%] gap-5 overflow-hidden">
      <!-- Glass morphism effect with image overlay - Modified to fade to the right -->
      <div class="absolute inset-0 bg-right bg-cover opacity-10 z-0" 
           style="background-image: linear-gradient(to right, 
                   rgba(255, 255, 255, 0.9) 0%, 
                   rgba(255, 255, 255, 0.6) 40%, 
                   rgba(255, 255, 255, 0.1) 100%), 
                   url('{{ asset('images/slide/o2.jpeg') }}'); 
                   filter: blur(4px);"></div>
      <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-24 h-24 object-contain relative z-10">
      <h1 class="text-[2rem] text-white font-extrabold relative z-10">Welcome Back!!!</h1>
      <p class="text-white text-center cursor-none relative z-10">This is exclusively for GCC interns</p>
      <a href="{{ url('/register') }}" class="relative inline-flex overflow-hidden rounded-full group border-2 border-white z-10">
          <span class="relative z-10 flex items-center justify-center px-7 py-3 font-medium text-white transition-colors duration-300 group-hover:text-[#196b3a]">
              <span class="absolute inset-0 w-full h-full bg-white -translate-x-full -skew-x-12 origin-left transition-transform duration-500 ease-in-out group-hover:translate-x-0"></span>
              <span class="relative z-20">Sign up</span>
          </span>
      </a>
    </div>
  </form>

  <!-- Mobile Version - Redesigned with Full-Width Wave Header -->
  <form method="POST" action="{{ route('login.store') }}" id="login-form-mobile" class="sm:hidden bg-white shadow-lg w-full max-w-md mx-auto my-8 p-0 rounded-2xl relative z-10 overflow-hidden">
    @csrf
    
    <!-- Wavy top section with logo - MODIFIED FOR FULL WIDTH -->
    <div class="relative">
      <!-- Wave background - Increased height and made full width -->
      <div class="bg-gradient-to-r from-emerald-600 via-emerald-500 to-emerald-400 h-64 w-full overflow-hidden">
        <!-- Multiple wave SVGs for layered effect - Positioned lower -->
        <div class="absolute bottom-0 left-0 w-full">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="w-full h-32 opacity-20 -mb-1">
            <path fill="#ffffff" fill-opacity="1" d="M0,128L48,144C96,160,192,192,288,197.3C384,203,480,181,576,165.3C672,149,768,139,864,154.7C960,171,1056,213,1152,213.3C1248,213,1344,171,1392,149.3L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
          </svg>
        </div>
        <div class="absolute bottom-0 left-0 w-full">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="w-full h-28 opacity-30">
            <path fill="#ffffff" fill-opacity="1" d="M0,224L60,213.3C120,203,240,181,360,186.7C480,192,600,224,720,229.3C840,235,960,213,1080,202.7C1200,192,1320,192,1380,192L1440,192L1440,320L1380,320C1320,320,1200,320,1080,320C960,320,840,320,720,320C600,320,480,320,360,320C240,320,120,320,60,320L0,320Z"></path>
          </svg>
        </div>
        <div class="absolute bottom-0 left-0 w-full">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="w-full h-24">
            <path fill="#ffffff" fill-opacity="1" d="M0,288L48,272C96,256,192,224,288,213.3C384,203,480,213,576,229.3C672,245,768,267,864,261.3C960,256,1056,224,1152,213.3C1248,203,1344,213,1392,218.7L1440,224L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
          </svg>
        </div>
        
        <!-- Logo centered and moved down in the wave section -->
        <div class="absolute inset-0 flex flex-col items-center justify-center pt-4">
          <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-28 h-28 object-contain">
        </div>
      </div>
    </div>
    
    <!-- Login form content -->
    <div class="px-8 pt-4 pb-8">
      <div class="text-center mb-6">
        <h1 class="text-2xl text-emerald-700 font-bold">Welcome back!</h1>
        <p class="text-gray-600 text-sm">This is exclusively for GCC interns</p>
      </div>
      
      <div class="flex flex-col gap-5">
        <!-- Email/Student ID Field -->
        <div class="flex flex-col gap-2 w-full">
          <label for="login-input-mobile" class="text-gray-700 text-sm font-medium">Email or Student ID</label>
          <div class="relative w-full">
            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-emerald-500 pointer-events-none z-10 flex items-center justify-center h-5 w-5">
              <i class="fas fa-envelope"></i>
            </div>
            <input type="text" id="login-input-mobile" name="login"
              class="bg-[#d0efe7] pl-12 pr-6 py-4 rounded-full w-full 
                     focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all duration-200" 
              placeholder="Email or Student ID...">
          </div>
          @error('login')
            <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
          @enderror
        </div>
        
        <!-- Password Field with Toggle -->
        <div class="flex flex-col gap-2 w-full">
          <label for="password-input-mobile" class="text-gray-700 text-sm font-medium">Password</label>
          <div class="relative w-full">
            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-emerald-500 pointer-events-none z-10 flex items-center justify-center h-5 w-5">
              <i class="fas fa-lock"></i>
            </div>
            <input type="password" id="password-input-mobile" name="password"
                   class="bg-[#d0efe7] pl-12 pr-12 py-4 rounded-full w-full
                          focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all duration-200" 
                   placeholder="Password...">
            <button type="button" class="password-toggle absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-emerald-500 focus:outline-none z-10 flex items-center justify-center h-5 w-5">
              <i class="far fa-eye"></i>
            </button>
          </div>
          @error('password')
            <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
          @enderror
        </div>
        
        <!-- Remember me and Forgot password row -->
        <div class="flex items-center justify-between w-full px-1 mt-1">
          <div class="flex items-center gap-2">
            <input type="checkbox" name="remember" id="remember-mobile" class="text-emerald-500 focus:ring-emerald-500">
            <label for="remember-mobile" class="text-gray-600 text-sm">Remember me</label>
          </div>
          <a href="#" class="text-emerald-500 text-sm font-medium">Forgot password?</a>
        </div>
        
        <!-- Login button -->
        <button type="submit" 
          class="bg-gradient-to-r from-emerald-600 to-emerald-500 w-full h-14 rounded-full text-white mt-6 relative overflow-hidden
                 transition-all duration-200 ease-in-out active:scale-[0.95] transform
                 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-opacity-50
                 disabled:opacity-80 disabled:cursor-not-allowed login-button">
          <span class="button-text block transition-all duration-300 ease-in-out relative z-10">Login</span>
          <span class="button-loading absolute inset-0 flex items-center justify-center gap-2 
                      transition-all duration-300 ease-in-out opacity-0 invisible z-20">
            <i class="fas fa-spinner fa-spin"></i> Logging in...
          </span>
        </button>
        
        <!-- Sign up link -->
        <div class="text-center mt-6">
          <p class="text-gray-600 text-sm">New user? 
            <a href="{{ url('/register') }}" class="text-emerald-500 font-medium">Sign Up</a>
          </p>
        </div>
        
        <!-- Social media section removed as requested -->
      </div>
    </div>
  </form>

  <!-- JavaScript - Keep the existing JS but add focus styles -->
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
  
    // Loading state handling
    const desktopForm = document.getElementById('login-form-desktop');
    const mobileForm = document.getElementById('login-form-mobile');
    
    function showLoading(button) {
      const textElement = button.querySelector('.button-text');
      const loadingElement = button.querySelector('.button-loading');
      
      if (textElement && loadingElement) {
        // Don't hide the text completely, just make it transparent
        textElement.style.opacity = '0';
        
        // Make loading visible and opaque
        loadingElement.style.opacity = '1';
        loadingElement.classList.remove('invisible');
      }
      
      button.disabled = true;
    }
    
    function resetButton(button) {
      const textElement = button.querySelector('.button-text');
      const loadingElement = button.querySelector('.button-loading');
      
      if (textElement && loadingElement) {
        textElement.style.opacity = '1';
        loadingElement.style.opacity = '0';
        loadingElement.classList.add('invisible');
      }
      
      button.disabled = false;
    }
    
    if (desktopForm) {
      desktopForm.addEventListener('submit', function(e) {
        const button = this.querySelector('.login-button');
        showLoading(button);
      });
    }
    
    if (mobileForm) {
      mobileForm.addEventListener('submit', function(e) {
        const button = this.querySelector('.login-button');
        showLoading(button);
      });
    }
    
    // Password toggle functionality
    document.querySelectorAll('.password-toggle').forEach(button => {
      button.addEventListener('click', function() {
        const input = this.parentElement.querySelector('input');
        const icon = this.querySelector('i');
        
        if (input.type === 'password') {
          input.type = 'text';
          icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
          input.type = 'password';
          icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
        
        // Force focus back to input for better UX
        input.focus();
      });
    });
    
    // Ensure icons maintain position regardless of error messages
    const inputIcons = document.querySelectorAll('.fas.fa-envelope, .fas.fa-lock');
    inputIcons.forEach(icon => {
      const iconContainer = icon.closest('div');
      if (iconContainer) {
        iconContainer.style.display = 'flex';
        iconContainer.style.alignItems = 'center';
        iconContainer.style.justifyContent = 'center';
        iconContainer.style.height = '24px';
        iconContainer.style.width = '24px';
      }
    });
    
    // Mobile touch feedback
    document.querySelectorAll('.login-button').forEach(button => {
      button.addEventListener('touchstart', function() {
        this.classList.add('scale-[0.95]');
      });
      
      button.addEventListener('touchend', function() {
        this.classList.remove('scale-[0.95]');
      });
    });
    
    // Add focus styles for emerald theme
    const inputs = document.querySelectorAll('input[type="text"], input[type="password"]');
    inputs.forEach(input => {
      input.addEventListener('focus', function() {
        this.classList.add('ring-2', 'ring-emerald-500');
      });
      
      input.addEventListener('blur', function() {
        this.classList.remove('ring-2', 'ring-emerald-500');
      });
    });
  });
  </script>
@endsection