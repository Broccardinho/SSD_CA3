<!doctype html>
<html lang="{{ strreplace('', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Pokémon Trainer System</title>

    <!-- Replace Buny font with Pokémon font -->
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">

    <!-- Update Vite to load Tailwind instead of Bootstrap -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900">
<div id="app">
    <!-- Remove Bootstrap navbar -->
    @auth
        <nav class="p-4 bg-gray-800 border-b-4 border-yellow-400">
            <div class="container mx-auto flex justify-between items-center">
                <a href="{{ url('/') }}" class="text-yellow-400 text-sm font-press-start hover:text-yellow-300">
                    POKÉTRAINER v1.0
                </a>
                <div class="flex items-center space-x-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-red-400 text-sm font-press-start hover:text-red-300">
                            LOGOUT
                        </button>
                    </form>
                </div>
            </div>
        </nav>
    @endauth

    <main class="min-h-screen">
        @yield('content')
    </main>
</div>
</body>
</html>
