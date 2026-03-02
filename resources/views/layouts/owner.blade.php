<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} - FAMER para Owners</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-900 min-h-screen">
    {{-- Top Navigation --}}
    <nav class="bg-gray-800 border-b border-gray-700 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-6">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 text-white">
                        <span class="text-xl font-bold text-red-500">FAMER</span>
                        <span class="text-gray-400">|</span>
                        <span class="text-sm text-gray-400">Owner Dashboard</span>
                    </a>
                </div>
                
                <div class="flex items-center gap-4">
                    @auth
                        @if(auth()->user()->ownedRestaurants()->exists())
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg text-white text-sm">
                                    <span>{{ auth()->user()->ownedRestaurants()->first()->name ?? 'Mis Restaurantes' }}</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-56 bg-gray-800 rounded-lg shadow-lg border border-gray-700 py-2">
                                    @foreach(auth()->user()->ownedRestaurants as $rest)
                                        <a href="{{ url('/owner') }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700">{{ $rest->name }}</a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <a href="{{ route('profile') }}" class="text-gray-400 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-400 hover:text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            </button>
                        </form>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Sidebar + Content --}}
    <div class="flex">
        {{-- Sidebar --}}
        @if(isset($restaurant))
        <aside class="w-64 bg-gray-800 border-r border-gray-700 min-h-[calc(100vh-4rem)] hidden lg:block">
            <div class="p-4">
                <div class="flex items-center gap-3 mb-6">
                    @if($restaurant->logo_url)
                        <img src="{{ $restaurant->logo_url }}" alt="" class="w-10 h-10 rounded-lg object-cover">
                    @else
                        <div class="w-10 h-10 rounded-lg bg-red-600 flex items-center justify-center text-white font-bold">
                            {{ substr($restaurant->name, 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <h3 class="text-white font-semibold text-sm truncate" style="max-width: 160px;">{{ $restaurant->name }}</h3>
                        <span class="text-xs {{ $restaurant->is_claimed ? 'text-green-400' : 'text-gray-400' }}">
                            {{ $restaurant->is_claimed ? 'Verificado' : 'Sin verificar' }}
                        </span>
                    </div>
                </div>

                <nav class="space-y-1">
                    <a href="{{ url('/owner') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->is('owner*') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('owner.email-marketing', $restaurant) }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('owner.email-marketing') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>Email Marketing</span>
                    </a>
                    <a href="{{ route('owner.reviews', $restaurant) }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('owner.reviews') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        <span>Resenas</span>
                    </a>
                    <a href="{{ route('owner.menu', $restaurant) }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('owner.menu') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span>Menu</span>
                    </a>
                    <a href="{{ route('owner.famer', $restaurant) }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('owner.famer') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>FAMER Score</span>
                    </a>
                    
                    <div class="pt-4 mt-4 border-t border-gray-700">
                        <a href="{{ route('restaurants.show', $restaurant->slug) }}" target="_blank" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-400 hover:bg-gray-700 hover:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            <span>Ver Pagina Publica</span>
                        </a>
                    </div>
                </nav>
            </div>
        </aside>
        @endif

        {{-- Main Content --}}
        <main class="flex-1">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
      (function(d,t) {
        var BASE_URL="https://chat.mefimports.com";
        var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
        g.src=BASE_URL+"/packs/js/sdk.js";
        g.defer = true;
        s.parentNode.insertBefore(g,s);
        g.onload=function(){
            baseUrl: BASE_URL
          });
        }
      })(document,"script");
    </script>
</body>
</html>
