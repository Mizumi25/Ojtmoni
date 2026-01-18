<div x-data="{ openIndex: null }" style="z-index: 500;" class="h-full flex flex-col">
    <!-- Header (Modernized: No Background) -->
    <div class="flex justify-between items-center p-4 border-b">
        <span class="font-semibold text-gray-700">Notifications</span>
        <button @click="open = false" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Notifications List -->
    <div class="max-h-[80vh] overflow-y-auto">
        <template x-for="(notif, index) in @this.notifications" :key="index">
            
            <div x-data="{ swiped: false }"
                class="relative flex items-center p-4 py-6 border-b transition-transform duration-200"
                :class="openIndex === index ? '-translate-x-16' : ''"
                @touchstart.passive="openIndex = index"
                @mousedown="openIndex = index">

                <!-- Profile Picture (Left) -->
                <div class="w-12 h-12 rounded-full bg-gray-300 flex-shrink-0 overflow-hidden">
                    <img :src="notif.profile_picture" alt="Profile" class="w-full h-full object-cover">
                </div>

                <!-- Notification Content -->
                <div class="flex-1 ml-3">
                    <p class="text-sm text-gray-700">
                        <span class="font-semibold" x-text="notif.name"></span> 
                        <span x-text="notif.action"></span>
                    </p>
                    <span class="text-xs text-gray-400" x-text="notif.time"></span>
                </div>

                <!-- Green Dot (New Notifications) -->
                <div x-show="notif.isNew" class="w-3 h-3 bg-green-500 rounded-full absolute top-4 right-4"></div>

                <!-- Action Icons (Bottom Right) -->
                <div class="absolute bottom-2 right-4 flex gap-2 opacity-0 transition-opacity duration-200"
                     :class="openIndex === index ? 'opacity-100 flex' : 'hidden'">

                    <!-- View -->
                    <button class="text-gray-500" @click="alert('Viewing notification: ' + notif.name)">
                        <i class="fas fa-eye"></i>
                    </button>

                    <!-- Delete -->
                    <button class="text-gray-500" @click="$wire.deleteNotification(notif.id)">
                        <i class="fas fa-trash-alt"></i>
                    </button>

                </div>
            </div>

        </template>

        <!-- No Notifications Message -->
        <p class="text-gray-500 text-center py-4" x-show="false">No new notifications</p>
    </div>
</div>
