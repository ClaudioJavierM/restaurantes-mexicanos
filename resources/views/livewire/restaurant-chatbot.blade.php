<div>
    <!-- Chat Toggle Button -->
    <div class="fixed bottom-6 right-6 z-50" x-data>
        @if(!$isOpen)
        <button
            wire:click="toggleChat"
            class="bg-gradient-to-r from-red-600 to-red-500 text-white rounded-full p-4 shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 flex items-center gap-2 group"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <span class="max-w-0 overflow-hidden group-hover:max-w-xs transition-all duration-500 ease-in-out whitespace-nowrap text-sm font-medium">
                {{ $locale === 'en' ? 'Chat with us' : 'Chatea con nosotros' }}
            </span>
        </button>
        @endif
    </div>

    <!-- Chat Window -->
    @if($isOpen)
    <div class="fixed bottom-6 right-6 z-50 w-80 sm:w-96 flex flex-col bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden" style="max-height: 500px;">
        <!-- Header -->
        <div class="bg-gradient-to-r from-red-600 to-red-500 px-4 py-3 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-white font-semibold text-sm">{{ $restaurant->name }}</h3>
                    <div class="flex items-center gap-1">
                        <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                        <span class="text-white/80 text-xs">{{ $locale === 'en' ? 'Online 24/7' : 'En linea 24/7' }}</span>
                    </div>
                </div>
            </div>
            <button wire:click="toggleChat" class="text-white/80 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Messages -->
        <div
            class="flex-1 overflow-y-auto p-4 space-y-3"
            style="max-height: 340px;"
            id="chatbot-messages"
            wire:poll.visible="$refresh"
        >
            @foreach($messages as $msg)
                @if($msg['role'] === 'assistant')
                <div class="flex items-start gap-2">
                    <div class="w-7 h-7 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <div class="bg-gray-100 rounded-2xl rounded-tl-md px-4 py-2.5 max-w-[80%]">
                        <p class="text-sm text-gray-800 whitespace-pre-line">{{ $msg['content'] }}</p>
                    </div>
                </div>
                @else
                <div class="flex justify-end">
                    <div class="bg-red-600 text-white rounded-2xl rounded-tr-md px-4 py-2.5 max-w-[80%]">
                        <p class="text-sm whitespace-pre-line">{{ $msg['content'] }}</p>
                    </div>
                </div>
                @endif
            @endforeach

            @if($isLoading)
            <div class="flex items-start gap-2">
                <div class="w-7 h-7 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <div class="bg-gray-100 rounded-2xl rounded-tl-md px-4 py-3">
                    <div class="flex gap-1.5">
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Quick Actions -->
        @if(count($messages) <= 1)
        <div class="px-4 pb-2 flex flex-wrap gap-1.5 flex-shrink-0">
            @php
                $quickActions = $locale === 'en'
                    ? ['Menu' => 'What\'s on the menu?', 'Hours' => 'What are your hours?', 'Location' => 'Where are you located?', 'Reserve' => 'Can I make a reservation?']
                    : ['Menu' => 'Que hay en el menu?', 'Horarios' => 'Cuales son sus horarios?', 'Ubicacion' => 'Donde estan ubicados?', 'Reservar' => 'Puedo hacer una reservacion?'];
            @endphp
            @foreach($quickActions as $label => $question)
                <button
                    wire:click="$set('userMessage', '{{ $question }}')"
                    x-on:click="$nextTick(() => $wire.sendMessage())"
                    class="text-xs bg-red-50 text-red-700 px-3 py-1.5 rounded-full hover:bg-red-100 transition-colors border border-red-200"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>
        @endif

        <!-- Input -->
        <div class="border-t border-gray-200 p-3 flex-shrink-0">
            <form wire:submit="sendMessage" class="flex gap-2">
                <input
                    type="text"
                    wire:model="userMessage"
                    placeholder="{{ $locale === 'en' ? 'Type your question...' : 'Escribe tu pregunta...' }}"
                    class="flex-1 text-sm border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                    autocomplete="off"
                    @if($isLoading) disabled @endif
                >
                <button
                    type="submit"
                    class="bg-red-600 text-white rounded-full p-2 hover:bg-red-700 transition-colors disabled:opacity-50"
                    @if($isLoading) disabled @endif
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </form>
            <p class="text-center text-[10px] text-gray-400 mt-1.5">AI Assistant &bull; {{ $locale === 'en' ? 'Responses may not be 100% accurate' : 'Las respuestas pueden no ser 100% exactas' }}</p>
        </div>
    </div>
    @endif

    <!-- Auto-scroll script -->
    <script>
        document.addEventListener('livewire:updated', () => {
            const el = document.getElementById('chatbot-messages');
            if (el) el.scrollTop = el.scrollHeight;
        });
    </script>
</div>
