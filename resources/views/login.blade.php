@extends('layouts.app')

@section('content')
    <head>
        <!-- Removed Vite CSS reference - we'll use CDN for Tailwind -->
        @vite(['resources/js/app.js'])
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
        <!-- Using Tailwind from CDN for simplicity -->
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            .poke-container {
                font-family: 'Press Start 2P', cursive;
                font-size: 0.8rem;
            }
            .poke-input {
                border: 2px solid #1e40af;
                border-radius: 0.5rem;
                padding: 0.75rem 1rem;
                width: 100%;
                font-size: 0.8rem;
            }
            .poke-button {
                background-color: #dc2626;
                color: white;
                border: 2px solid black;
                border-radius: 0.5rem;
                padding: 0.75rem;
                width: 100%;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                transition: all 0.2s;
                font-size: 0.8rem;
            }
            .poke-button:hover {
                background-color: #b91c1c;
                transform: scale(1.02);
            }
            .error-border {
                border-color: #ef4444 !important;
            }
            .error-text {
                color: #ef4444;
                font-size: 0.6rem;
                margin-top: 0.25rem;
            }
        </style>
    </head>
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-center">
            <div class="w-full max-w-md bg-white rounded-lg border-4 border-blue-800 p-6 poke-container">
                <div class="text-center mb-6">
                    <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/poke-ball.png"
                         alt="Pokéball"
                         class="h-12 w-12 mx-auto mb-4">
                    <h1 class="text-red-600 text-xl">TRAINER LOGIN</h1>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm mb-2">POKÉMON EMAIL</label>
                        <input id="email" type="email"
                               class="poke-input @error('email') error-border @enderror"
                               name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                        <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="password" class="block text-gray-700 text-sm mb-2">SECRET CODE</label>
                        <input id="password" type="password"
                               class="poke-input @error('password') error-border @enderror"
                               name="password" required autocomplete="current-password">
                        @error('password')
                        <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <div class="flex items-center">
                            <input class="mr-2" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="text-gray-700 text-xs" for="remember">REMEMBER ME</label>
                        </div>
                    </div>

                    <div class="flex flex-col space-y-4 items-center">
                        <button type="submit" class="poke-button">
                            LOGIN
                        </button>

                        @if (Route::has('password.request'))
                            <a class="text-blue-600 hover:text-blue-800 text-xs" href="{{ route('password.request') }}">
                                FORGOT YOUR SECRET CODE?
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
