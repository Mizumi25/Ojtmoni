<div x-data="{ activeConversation: null }" class="h-screen flex bg-gray-100 w-full overflow-hidden">
    <div class="w-full md:w-1/3 bg-white border-r px-3 flex flex-col" :class="{ 'md:w-full': !activeConversation }">
        <div class="flex justify-between items-center p-4 border-b">
            <span class="font-semibold text-gray-700 text-lg">Messages</span>
            <button class="text-gray-500">
                <i class="fas fa-ellipsis-v"></i>
            </button>
        </div>

        
        

        <p class="p-4 text-gray-600 font-semibold">Students</p>

        <div class="p-2">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" placeholder="Search"
                       class="w-full pl-10 p-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-1 focus:ring-blue-400">
            </div>
        </div>
        
        <div class="p-4 border-b bg-gray-100" wire:key="coordinator-list">
            <p class="text-gray-600 font-semibold">Coordinator</p>
            @foreach ($conversations->where('type', 'user')->filter(fn($c) => \App\Models\User::find($c['id'])?->isCoordinator()) as $coordinator)
                <div @click="activeConversation = { id: '{{ $coordinator['id'] }}', type: 'user', name: '{{ $coordinator['name'] }}' }; $wire.selectConversation({ id: '{{ $coordinator['id'] }}', type: 'user', name: '{{ $coordinator['name'] }}' })"
                     class="flex items-center mt-2 space-x-2 cursor-pointer hover:bg-gray-200 p-2 rounded">
                    <div class="w-12 h-12 rounded-full bg-blue-300 flex items-center justify-center">
                        @if (\App\Models\User::find($coordinator['id'])?->profile_picture)
                            <img src="{{ asset('storage/' . \App\Models\User::find($coordinator['id'])->profile_picture) }}" alt="{{ $coordinator['name'] }}" class="w-full h-full rounded-full object-cover">
                        @else
                            <span class="text-white font-semibold">{{ strtoupper(substr($coordinator['name'], 0, 2)) }}</span>
                        @endif
                    </div>
                    <p class="text-gray-800 font-medium">{{ $coordinator['name'] }}</p>
                </div>
            @endforeach

        <div class="flex-1 overflow-y-auto">
            @foreach ($conversations->sortByDesc('updated_at') as $conversation)
                @if ($conversation['type'] === 'user' && (!\App\Models\User::find($conversation['id'])?->isCoordinator()))
                    <div @click="activeConversation = { id: '{{ $conversation['id'] }}', type: 'user', name: '{{ $conversation['name'] }}' }; $wire.selectConversation({ id: '{{ $conversation['id'] }}', type: 'user', name: '{{ $conversation['name'] }}' })"
                         class="flex items-center p-4 border-b cursor-pointer hover:bg-gray-100"
                         :class="{ 'bg-gray-100': activeConversation && activeConversation.id === '{{ $conversation['id'] }}' && activeConversation.type === 'user' }"
                         wire:key="user-{{ $conversation['id'] }}">
                        <div class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center">
                            @if (\App\Models\User::find($conversation['id'])?->profile_picture)
                                <img src="{{ asset('storage/' . \App\Models\User::find($conversation['id'])->profile_picture) }}" alt="{{ $conversation['name'] }}" class="w-full h-full rounded-full object-cover">
                            @else
                                <span class="text-gray-700 font-semibold">{{ strtoupper(substr($conversation['name'], 0, 2)) }}</span>
                            @endif
                        </div>
                        <div class="flex-1 ml-3">
                            <p class="font-semibold text-gray-800">{{ $conversation['name'] }}</p>
                            <p class="text-sm text-gray-500 truncate">
                                <span class="text-green-500 italic" x-show="false">Typing...</span>
                                <span x-show="true">{{-- Last message preview --}}</span>
                            </p>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="text-xs text-gray-400">{{ now()->diffForHumans() }}</span>
                            <template x-if="false">
                                <span class="w-5 h-5 bg-red-500 text-white text-xs flex items-center justify-center rounded-full">2</span>
                            </template>
                            <template x-if="true">
                                <i class="fas fa-check text-gray-400"></i>
                            </template>
                        </div>
                    </div>
                @elseif ($conversation['type'] === 'group')
                    <div @click="activeConversation = { id: '{{ $conversation['id'] }}', type: 'group', name: '{{ $conversation['name'] }}' }; $wire.selectConversation({ id: '{{ $conversation['id'] }}', type: 'group', name: '{{ $conversation['name'] }}' })"
                         class="flex items-center p-4 border-b cursor-pointer hover:bg-gray-100"
                         :class="{ 'bg-gray-100': activeConversation && activeConversation.id === '{{ $conversation['id'] }}' && activeConversation.type === 'group' }"
                         wire:key="group-{{ $conversation['id'] }}">
                        <div class="w-12 h-12 rounded-full bg-green-300 flex items-center justify-center">
                            <i class="fas fa-users text-white"></i>
                        </div>
                        <div class="flex-1 ml-3">
                            <p class="font-semibold text-gray-800">{{ $conversation['name'] }}</p>
                            <p class="text-sm text-gray-500 truncate">
                                <span class="text-green-500 italic" x-show="false">Typing...</span>
                                <span x-show="true">{{-- Last group message preview --}}</span>
                            </p>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="text-xs text-gray-400">{{ now()->diffForHumans() }}</span>
                            <template x-if="false">
                                <span class="w-5 h-5 bg-red-500 text-white text-xs flex items-center justify-center rounded-full">3</span>
                            </template>
                            <i class="fas fa-check-double text-gray-400"></i>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <div x-show="activeConversation" x-transition:enter="transition transform ease-out duration-300"
         x-transition:enter-start="translate-x-full opacity-0"
         x-transition:enter-end="translate-x-0 opacity-100"
         class="w-full md:w-2/3 h-full bg-gray-50 fixed md:relative right-0 top-0 border-l shadow-md flex flex-col">

        <div class="flex justify-between items-center p-4 bg-white border-b">
            <p class="font-semibold text-gray-800 text-lg" x-text="activeConversation ? activeConversation.name : ''"></p>
            <div class="flex space-x-4">
                <button class="text-gray-500"><i class="fas fa-phone"></i></button>
                <button class="text-gray-500"><i class="fas fa-video"></i></button>
                <button class="text-gray-500"><i class="fas fa-ellipsis-h"></i></button>
            </div>
        </div>

        <div class="text-center text-xs text-gray-500 py-2">
            <span class="bg-white px-3 py-1 rounded-lg shadow-sm">{{ now()->format('F j') }}</span>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-4" wire:loading.class="opacity-50">
            @if ($selectedConversation && $selectedConversation['type'] === 'user')
              @forelse ($messages as $message)
                    <div class="flex items-end {{ $message->sender_id == auth()->id() ? 'justify-end' : 'justify-start' }}">
                        @if ($message->sender_id != auth()->id())
                            <div class="w-8 h-8 rounded-full mr-2 flex-shrink-0">
                                @if ($message->sender->profile_picture)
                                    <img src="{{ asset('storage/' . $message->sender->profile_picture) }}" alt="{{ $message->sender->name }}" class="w-full h-full rounded-full object-cover">
                                @else
                                    <div class="w-full h-full rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-gray-700 text-xs font-semibold">{{ strtoupper(substr($message->sender->name, 0, 2)) }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif
                        <div class="max-w-xs md:max-w-sm p-3 rounded-lg shadow-md {{ $message->sender_id == auth()->id() ? 'bg-emerald-100 text-gray-700 order-1' : 'bg-white text-gray-700 order-1' }}">
                            @if ($message->type === 'text')
                                <p>{{ $message->content }}</p>
                            @elseif ($message->type === 'image' && $message->media_path)
                                <img src="{{ asset('storage/' . $message->media_path) }}" alt="Image" class="rounded">
                            @elseif ($message->type === 'video' && $message->media_path)
                                <video src="{{ asset('storage/' . $message->media_path) }}" controls class="rounded max-w-full"></video>
                            @elseif ($message->type === 'file' && $message->media_path)
                                <a href="{{ asset('storage/' . $message->media_path) }}" target="_blank" class="text-blue-500 underline">
                                    View File
                                </a>
                            @endif
                        </div>
                        <span class="text-xs text-gray-400 ml-2 {{ $message->sender_id == auth()->id() ? 'order-2 mr-2' : 'order-2' }}">{{ $message->created_at->format('h:i A') }}</span>
                    </div>
                @empty
                    <div class="text-center text-gray-500 py-4">
                        No messages yet. Start a conversation!
                    </div>
                @endforelse
            
            @elseif ($selectedConversation && $selectedConversation['type'] === 'group')
                @forelse ($messages as $message)
                    <div class="flex flex-col {{ $message->sender_id == auth()->id() ? 'items-end' : 'items-start' }}">
                        <div class="flex items-center space-x-2 mb-1">
                            @if ($message->sender_id != auth()->id())
                                <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center">
                                    @if ($message->sender->profile_picture)
                                        <img src="{{ asset('storage/' . $message->sender->profile_picture) }}" alt="{{ $message->sender->name }}" class="w-full h-full rounded-full object-cover">
                                    @else
                                        <span class="text-xs font-bold text-white">{{ strtoupper(substr($message->sender->name, 0, 2)) }}</span>
                                    @endif
                                </div>
                                <span class="text-xs text-gray-600">{{ $message->sender->name }}</span>
                            @endif
                        </div>
                        <div class="max-w-xs md:max-w-sm p-3 rounded-lg shadow-md {{ $message->sender_id == auth()->id() ? 'bg-blue-100 text-gray-700' : 'bg-white text-gray-700' }}">
                            @if ($message->type === 'text')
                                <p>{{ $message->content }}</p>
                            @elseif ($message->type === 'image' && $message->media_path)
                                <img src="{{ asset('storage/' . $message->media_path) }}" alt="Image" class="rounded">
                            @elseif ($message->type === 'video' && $message->media_path)
                                <video src="{{ asset('storage/' . $message->media_path) }}" controls class="rounded max-w-full"></video>
                            @elseif ($message->type === 'file' && $message->media_path)
                                <a href="{{ asset('storage/' . $message->media_path) }}" target="_blank" class="text-blue-500 underline">
                                    View File
                                </a>
                            @endif
                        </div>
                        <span class="text-xs text-gray-400 mt-1">{{ $message->created_at->format('h:i A') }}</span>
                    </div>
                @empty
                    <div class="text-center text-gray-500 py-4">
                        No group messages yet. Start the conversation!
                    </div>
                @endforelse
            @endif
            <div x-init="$el.scrollTop = $el.scrollHeight" @scroll.debounce.500ms="$dispatch('scroll-to-bottom')"></div>
        </div>

        <div class="p-4 border-t bg-white sticky bottom-0">
            <form wire:submit.prevent="sendMessage">
                <div class="flex items-center">
                    <input type="text" wire:model="newMessageText" placeholder="Type a message..."
                           class="flex-1 p-2 rounded-full border border-gray-300 focus:outline-none focus:ring-1 focus:ring-green-400">
                    <label for="file-upload" class="ml-2 cursor-pointer text-gray-500">
                        <i class="fas fa-paperclip"></i>
                    </label>
                    <input id="file-upload" type="file" class="hidden" wire:model.live="newMessageFile">
                    <button type="submit" class="ml-2 text-green-500" :disabled="!newMessageText && !newMessageFile">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
                @error('newMessageFile') <span class="error">{{ $message }}</span> @enderror
            </form>
        </div>
        <div x-show="!activeConversation" class="absolute inset-0 bg-gray-50 flex items-center justify-center">
            <p class="text-gray-500 italic">Select a conversation to view messages.</p>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        Livewire.on('scroll-to-bottom', () => {
            const chatContainer = document.querySelector('.overflow-y-auto');
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        });
    </script>
@endpush