<div class="bg-white shadow-md">
    <div class="w-full h-40 bg-gray-300 relative">
        <div class="max-w-5xl mx-auto px-6">
            <div class="flex items-end space-x-6 relative -bottom-12">
                <!-- Profile Picture -->
                <div class="relative w-32 h-32">
                    @if ($editing)
                        <label for="new_profile_picture" class="cursor-pointer group">
                            <div class="w-32 h-32 rounded-full border-4 border-white overflow-hidden bg-emerald-100 flex items-center justify-center relative">
                                @if ($new_profile_picture)
                                    <img src="{{ $new_profile_picture->temporaryUrl() }}" class="object-cover w-full h-full" />
                                @elseif ($profile_picture)
                                    <img src="{{ asset('storage/' . $profile_picture) }}" class="object-cover w-full h-full" />
                                @else
                                    <i class="fas fa-user text-emerald-600 text-3xl"></i>
                                @endif
                
                                <!-- Pencil Icon -->
                                <div class="absolute bottom-1 right-1 bg-white rounded-full p-1 shadow">
                                    <i class="fas fa-pencil-alt text-emerald-600 text-xs"></i>
                                </div>
                            </div>
                        </label>
                    @else
                        <div class="w-32 h-32 rounded-full border-4 border-white overflow-hidden bg-emerald-100 flex items-center justify-center">
                            @if ($new_profile_picture)
                                <img src="{{ $new_profile_picture->temporaryUrl() }}" class="object-cover w-full h-full" />
                            @elseif ($profile_picture)
                                <img src="{{ asset('storage/' . $profile_picture) }}" class="object-cover w-full h-full" />
                            @else
                                <i class="fas fa-user text-emerald-600 text-3xl"></i>
                            @endif
                        </div>
                    @endif
                
                    @if ($editing)
                        <input id="new_profile_picture" type="file" wire:model="new_profile_picture" class="hidden" />
                        @if ($new_profile_picture)
                            <button wire:click="savePicture" class="mt-2 text-sm bg-emerald-500 text-white px-3 py-1 rounded">Save Pic</button>
                        @endif
                    @endif
                </div>


                <!-- Profile Details -->
                <div class="flex-1 pt-6">
                    <h1 class="text-xl font-bold">{{ $name }}</h1>
                    <p class="text-sm text-gray-600">{{ $email }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="mt-20 max-w-5xl mx-auto bg-white shadow-md p-6">
        <div class="flex justify-between items-center border-b pb-4">
            <h2 class="text-lg font-semibold">User Account</h2>
            @if (!$editing)
                <button wire:click="edit" class="px-4 py-2 bg-blue-500 text-white rounded-md">Update Profile</button>
            @else
                <div class="flex gap-2">
                    <button wire:click="save" class="px-4 py-2 bg-green-500 text-white rounded-md">Save</button>
                    <button wire:click="cancel" class="px-4 py-2 bg-gray-300 text-black rounded-md">Cancel</button>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-3 gap-6 pt-6">
            <!-- Password Panel (stubbed) -->
            <div class="border border-gray-300 p-6 rounded-md">
              <h3 class="text-lg font-semibold mb-4">Change Password</h3>
          
              @if (session()->has('message'))
                  <div class="mb-4 text-green-600">{{ session('message') }}</div>
              @endif
          
              <div class="space-y-4">
                  <div>
                      <label class="block text-sm font-medium text-gray-700">Current Password</label>
                      <input type="password" wire:model.defer="current_password" class="w-full p-2 border border-gray-300 rounded-md">
                      @error('current_password') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                  </div>
                  <div>
                      <label class="block text-sm font-medium text-gray-700">New Password</label>
                      <input type="password" wire:model.defer="new_password" class="w-full p-2 border border-gray-300 rounded-md">
                      @error('new_password') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                  </div>
                  <div>
                      <label class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                      <input type="password" wire:model.defer="confirm_password" class="w-full p-2 border border-gray-300 rounded-md">
                  </div>
                  <div class="pt-2">
                      <button wire:click="updatePassword" class="bg-indigo-600 text-white px-4 py-2 rounded-md">Update Password</button>
                  </div>
              </div>
          </div>


            <!-- Profile Info -->
            <div class="col-span-2 border border-gray-300 p-6 rounded-md">
                <h3 class="text-lg font-semibold mb-4">Profile Information</h3>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" wire:model.defer="name" class="mt-1 w-full p-2 border rounded-md {{ $editing ? 'border-gray-300 bg-white' : 'border-transparent bg-gray-100' }}" {{ $editing ? '' : 'disabled' }}>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" value="{{ $email }}" class="mt-1 w-full p-2 border border-transparent bg-gray-100 rounded-md" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input type="text" wire:model.defer="phone_number" class="mt-1 w-full p-2 border rounded-md {{ $editing ? 'border-gray-300 bg-white' : 'border-transparent bg-gray-100' }}" {{ $editing ? '' : 'disabled' }}>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">OJT Agency</label>
                        <input type="text" value="{{ $ojt_info }}" class="mt-1 w-full p-2 border border-transparent bg-gray-100 rounded-md" disabled>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
