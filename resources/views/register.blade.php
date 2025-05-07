@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8 flex justify-center items-center min-h-screen">
        <!-- PC Screen Container -->
        <div class="w-full max-w-lg bg-gray-200 rounded-lg border-8 border-gray-600 shadow-2xl p-6 relative"
             style="font-family: 'Press Start 2P', cursive; background-image: url('https://www.transparenttextures.com/patterns/pixel-pattern.png');">
            <!-- Screen Border -->
            <div class="bg-gray-800 rounded-lg border-4 border-blue-800 p-4">
                <!-- Header -->
                <div class="text-center mb-6">
                    <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/poke-ball.png"
                         alt="Pokéball"
                         class="h-12 w-12 mx-auto mb-4 pixel-art"
                         style="image-rendering: pixelated;">
                    <h1 class="text-green-400 text-lg tracking-wider text-shadow">TRAINER REGISTRATION SYSTEM</h1>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Trainer Name -->
                    <div class="mb-4">
                        <label for="name" class="block text-green-400 text-xs mb-2">TRAINER NAME</label>
                        <input id="name" type="text"
                               class="w-full px-3 py-2 bg-gray-100 border-4 border-gray-400 rounded-none text-gray-800 text-xs font-mono @error('name') border-red-500 @enderror"
                               name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                        @error('name')
                        <p class="text-red-500 text-xs mt-1 bg-gray-800 p-1 border-2 border-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Pokémon Email -->
                    <div class="mb-4">
                        <label for="email" class="block text-green-400 text-xs mb-2">POKÉMON EMAIL</label>
                        <input id="email" type="email"
                               class="w-full px-3 py-2 bg-gray-100 border-4 border-gray-400 rounded-none text-gray-800 text-xs font-mono @error('email') border-red-500 @enderror"
                               name="email" value="{{ old('email') }}" required autocomplete="email">
                        @error('email')
                        <p class="text-red-500 text-xs mt-1 bg-gray-800 p-1 border-2 border-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Secret Code -->
                    <div class="mb-4">
                        <label for="password" class="block text-green-400 text-xs mb-2">SECRET CODE</label>
                        <input id="password" type="password"
                               class="w-full px-3 py-2 bg-gray-100 border-4 border-gray-400 rounded-none text-gray-800 text-xs font-mono @error('password') border-red-500 @enderror"
                               name="password" required autocomplete="new-password">
                        @error('password')
                        <p class="text-red-500 text-xs mt-1 bg-gray-800 p-1 border-2 border-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Code -->
                    <div class="mb-6">
                        <label for="password-confirm" class="block text-green-400 text-xs mb-2">CONFIRM CODE</label>
                        <input id="password-confirm" type="password"
                               class="w-full px-3 py-2 bg-gray-100 border-4 border-gray-400 rounded-none text-gray-800 text-xs font-mono"
                               name="password_confirmation" required autocomplete="new-password">
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-center">
                        <button type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white text-sm py-3 px-6 border-4 border-black shadow-lg transform hover:scale-105 transition-transform">
                            REGISTER
                        </button>
                    </div>
                </form>

                <!-- PC Details -->
                <div class="absolute top-2 right-2">
                    <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/25.png"
                         alt="Pikachu"
                         class="h-8 w-8 pixel-art"
                         style="image-rendering: pixelated;">
                </div>
            </div>
        </div>
    </div>
@endsection
