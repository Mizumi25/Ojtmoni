<div class="relative flex items-center justify-center min-h-screen bg-gray-100 w-full overflow-hidden">

    <!-- Emerald Glow Behind Modal -->
    <div class="absolute w-[500px] h-[500px] rounded-full bg-emerald-100 blur-3xl opacity-60 z-0"></div>

    <!-- Modal -->
    <div class="relative bg-white shadow-xl border border-gray-200 rounded-xl p-10 w-full max-w-md text-center space-y-6 z-10">

        <div class="w-24 h-24 rounded-full mx-auto">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-cover rounded-full">
        </div>

        <!-- Heading -->
        <h1 class="text-2xl font-bold text-gray-800">Pending Approval</h1>

        <!-- Description -->
        <p class="text-gray-600">
            Your account is currently pending approval. Please wait while the coordinator reviews your registration.
        </p>

        <!-- Buttons -->
        <div class="flex gap-4 justify-center">
            <!-- Contact Button -->
            <a href="/contact" class="flex-1 bg-emerald-500 text-white font-semibold py-2 px-4 rounded-full text-center hover:bg-emerald-600 transition">
                Contact
            </a>

            <!-- Logout Button -->
            <form method="POST" action="{{ route('logout') }}" class="flex-1">
                @csrf
                <button type="submit"
                        class="w-full border border-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-full hover:bg-gray-100 transition">
                    Logout
                </button>
            </form>
        </div>

    </div>
</div>
