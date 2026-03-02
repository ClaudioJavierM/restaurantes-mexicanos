<div x-data="chatData()" 
     x-init="initChat()"
     class="fixed bottom-4 right-4 z-50">

    {{-- Chat Bubble --}}
    <div x-show="showBubble && !$wire.isOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-75"
         x-transition:enter-end="opacity-100 scale-100"
         class="relative">
        
        <div class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full animate-pulse"></div>
        
        <button wire:click="openChat" 
                class="flex items-center gap-3 bg-gradient-to-r from-red-600 to-orange-500 text-white px-5 py-3 rounded-full shadow-2xl hover:shadow-red-500/25 hover:scale-105 transition-all duration-300">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <span class="font-medium">Que buscas hoy?</span>
        </button>
    </div>

    {{-- Chat Window --}}
    <div x-show="$wire.isOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="w-[380px] h-[520px] bg-white rounded-2xl shadow-2xl flex flex-col overflow-hidden border border-gray-200">
        
        {{-- Header --}}
        <div class="bg-gradient-to-r from-red-600 to-orange-500 text-white p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0A1.75 1.75 0 013 15.546V7.75A.75.75 0 013.75 7h16.5a.75.75 0 01.75.75v7.796z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold">Asistente de Restaurantes</h3>
                    <p class="text-xs text-white/80">
                        @if($userCity)
                            {{ $userCity }}
                        @else
                            Te ayudo a encontrar comida
                        @endif
                    </p>
                </div>
            </div>
            <button wire:click="closeChat" class="p-2 hover:bg-white/20 rounded-full transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Messages --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50" id="chat-messages">
            @foreach($messages as $message)
                @if($message["type"] === "user")
                    <div class="flex justify-end">
                        <div class="bg-red-600 text-white px-4 py-2 rounded-2xl rounded-br-md max-w-[80%]">
                            {{ $message["text"] }}
                        </div>
                    </div>
                @elseif($message["type"] === "bot")
                    <div class="flex justify-start">
                        <div class="bg-white border border-gray-200 px-4 py-2 rounded-2xl rounded-bl-md max-w-[80%] shadow-sm">
                            {{ $message["text"] }}
                        </div>
                    </div>
                @elseif($message["type"] === "restaurants")
                    <div class="space-y-3">
                        @foreach($message["cards"] as $restaurant)
                            <a href="{{ route("restaurant.show", $restaurant["slug"]) }}" 
                               class="block bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition group">
                                <div class="flex">
                                    <img src="{{ $restaurant["image"] }}" 
                                         alt="{{ $restaurant["name"] }}"
                                         class="w-24 h-24 object-cover"
                                         onerror="this.src='{{ asset("images/restaurant-placeholder.jpg") }}'">
                                    <div class="p-3 flex-1">
                                        <h4 class="font-bold text-gray-900 group-hover:text-red-600 transition text-sm">
                                            {{ $restaurant["name"] }}
                                        </h4>
                                        <div class="flex items-center gap-1 mt-1">
                                            <span class="text-yellow-500">★</span>
                                            <span class="text-xs text-gray-600">{{ number_format($restaurant["rating"], 1) }}</span>
                                            <span class="text-xs text-gray-400">({{ $restaurant["reviews"] }})</span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">{{ $restaurant["city"] }} - {{ $restaurant["category"] }}</p>
                                        @if(!empty($restaurant["popular_items"]))
                                            <p class="text-xs text-green-600 mt-1">{{ implode(", ", array_slice($restaurant["popular_items"], 0, 2)) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @elseif($message["type"] === "quick_replies")
                    <div class="flex flex-wrap gap-2">
                        @foreach($message["replies"] as $reply)
                            <button wire:click="selectQuickReply('{{ $reply }}')"
                                    class="px-3 py-1.5 bg-white border border-red-200 text-red-600 rounded-full text-sm hover:bg-red-50 transition">
                                {{ $reply }}
                            </button>
                        @endforeach
                    </div>
                @endif
            @endforeach

            @if($isTyping)
                <div class="flex justify-start">
                    <div class="bg-white border border-gray-200 px-4 py-3 rounded-2xl rounded-bl-md shadow-sm">
                        <div class="flex gap-1">
                            <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></span>
                            <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></span>
                            <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Input --}}
        <div class="p-4 bg-white border-t border-gray-200">
            <form wire:submit.prevent="sendMessage" class="flex gap-2">
                <input type="text" 
                       wire:model="userInput"
                       placeholder="Escribe que buscas..."
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent text-sm">
                <button type="submit" 
                        class="p-2 bg-red-600 text-white rounded-full hover:bg-red-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>

@script
<script>
    Alpine.data("chatData", () => ({
        showBubble: false,
        userLocation: { lat: null, lng: null, city: null, state: null },
        
        initChat() {
            setTimeout(() => { this.showBubble = true }, 5000);
            
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        this.userLocation.lat = pos.coords.latitude;
                        this.userLocation.lng = pos.coords.longitude;
                        
                        const url = "https://nominatim.openstreetmap.org/reverse?lat=" + pos.coords.latitude + "&lon=" + pos.coords.longitude + "&format=json";
                        
                        fetch(url)
                            .then(r => r.json())
                            .then(data => {
                                this.userLocation.city = data.address?.city || data.address?.town || data.address?.village;
                                this.userLocation.state = data.address?.state;
                                $wire.setUserLocation(this.userLocation.lat, this.userLocation.lng, this.userLocation.city, this.userLocation.state);
                            });
                    },
                    () => console.log("Location denied")
                );
            }
        }
    }));
    
    document.addEventListener("livewire:init", () => {
        Livewire.hook("morph.updated", () => {
            const container = document.getElementById("chat-messages");
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        });
    });
</script>
@endscript
