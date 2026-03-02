<!DOCTYPE html>
<html>
<head>
    <title>Test</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
    <nav>Navigation</nav>
    <main>
        @isset($slot)
            {{ $slot }}
        @else
            @yield('content')
        @endisset
    </main>
    <footer>Footer</footer>
    @livewireScripts
</body>
</html>
