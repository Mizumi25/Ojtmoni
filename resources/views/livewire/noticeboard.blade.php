<div class="w-full h-full p-4 bg-transparent space-y-4 relative" 
     x-data="{ images: @entangle('images'), showUpload: false }">
  <h2 class="text-xl font-bold text-emerald-600">Notice Board</h2>
    <div>
        @if ($user->hasRole(['admin', 'coordinator']))
           <!-- Floating Upload Icon -->
          <div class="absolute top-4 right-4 z-50">
              <button @click="showUpload = true"
                  class="w-12 h-12 bg-emerald-600 text-white rounded-full shadow-lg flex items-center justify-center hover:bg-emerald-700 transition">
                  <i class="fas fa-upload"></i>
              </button>
          </div>
          
          <!-- Slide-in Upload Panel -->
          <div x-show="showUpload"
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="transform translate-x-full opacity-0"
               x-transition:enter-end="transform translate-x-0 opacity-100"
               x-transition:leave="transition ease-in duration-200"
               x-transition:leave-start="transform translate-x-0 opacity-100"
               x-transition:leave-end="transform translate-x-full opacity-0"
               class="fixed top-0 right-0 w-full sm:w-1/2 h-full bg-white shadow-2xl z-40 p-6 overflow-y-auto"
               style="display: none;">
              <div class="flex justify-between items-center mb-4">
                  <h2 class="text-2xl font-bold text-emerald-600">Post Announcement</h2>
                  <button @click="showUpload = false" class="text-gray-500 hover:text-red-500 text-xl">
                      <i class="fas fa-times"></i>
                  </button>
              </div>
          
              <div class="space-y-4">
                  <input type="text" wire:model.defer="title" placeholder="Title"
                      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500" />
                  <textarea wire:model.defer="body" placeholder="What's on your mind?"
                      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 resize-none" rows="4"></textarea>
                  <div>
                      <input type="file" multiple wire:model="images" class="hidden" x-ref="imageInput">
                      <button type="button" class="bg-emerald-500 text-white rounded px-4 py-2 hover:bg-emerald-600"
                          x-on:click="$refs.imageInput.click()">Upload Photo</button>
                  </div>
          
                  <button wire:click="postAnnouncement"
                      class="w-full bg-emerald-600 text-white font-semibold rounded px-4 py-2 hover:bg-emerald-700">
                      Post
                  </button>
          
                  <!-- Previews -->
                  <div class="flex gap-2 flex-wrap mt-4">
                      @foreach ($images as $index => $img)
                          <div class="relative w-20 h-20">
                              <img src="{{ $img->temporaryUrl() }}" class="w-full h-full object-cover rounded-lg" />
                              <button type="button"
                                  class="absolute -top-2 -right-2 bg-white text-red-600 rounded-full px-1"
                                  wire:click="removePreview({{ $index }})">
                                  <i class="fas fa-times-circle"></i>
                              </button>
                          </div>
                      @endforeach
                  </div>
              </div>
          </div>


        @endif
    </div>

    <div class="flex gap-2">
        @if ($user->isStudent())
            <button wire:click="$set('filter', 'coordinators')" class="rounded bg-emerald-200 px-3 py-1">Coordinators</button>
            <button wire:click="$set('filter', 'admins')" class="rounded bg-emerald-200 px-3 py-1">Admins</button>
        @elseif ($user->isCoordinator())
            <button wire:click="$set('filter', 'me')" class="rounded bg-emerald-200 px-3 py-1">You</button>
            <button wire:click="$set('filter', 'admins')" class="rounded bg-emerald-200 px-3 py-1">Admins</button>
        @else
            <button wire:click="$set('filter', 'me')" class="rounded bg-emerald-200 px-3 py-1">You</button>
            <button wire:click="$set('filter', 'coordinators')" class="rounded bg-emerald-200 px-3 py-1">Coordinators</button>
        @endif
    </div>

    @foreach ($announcements as $post)
        <div class="bg-white rounded shadow p-4 space-y-2">
            <div class="flex justify-between">
                <div class="flex gap-2 items-center">
                    @if ($post->user->profile_picture)
                          <img src="{{ asset('storage/' . $post->user->profile_picture) }}" class="w-10 h-10 rounded-full">
                      @else
                          <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                              <i class="fas fa-user"></i>
                          </div>
                      @endif
                    <div>
                        <div class="font-bold">{{ $post->user->name }}</div>
                        <div class="text-sm text-gray-500">
                            @if ($post->created_at->gt(now()->subMinutes(1)))
                                Now
                            @elseif ($post->created_at->gt(now()->subHours(1)))
                                {{ $post->created_at->diffForHumans() }}
                            @else
                                {{ $post->created_at->format('M d, Y h:i A') }}
                            @endif
                        </div>
                    </div>
                </div>
                <button class="text-emerald-500">Share</button>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-emerald-700">{{ $post->title }}</h3>
                <p>{{ $post->body }}</p>
            </div>

            @if ($post->images->count())
                <div class="grid grid-cols-2 gap-2 mt-2">
                    <img src="{{ asset('storage/' . $post->images[0]->path) }}" class="w-full h-40 object-cover rounded col-span-2">
                    @if ($post->images->count() > 1)
                        <img src="{{ asset('storage/' . $post->images[1]->path) }}" class="w-full h-20 object-cover rounded">
                    @endif
                    @if ($post->images->count() > 2)
                        <div class="relative">
                            <img src="{{ asset('storage/' . $post->images[2]->path) }}" class="w-full h-20 object-cover rounded">
                            <div class="absolute inset-0 bg-black bg-opacity-50 text-white flex items-center justify-center rounded">View More</div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @endforeach
</div>
