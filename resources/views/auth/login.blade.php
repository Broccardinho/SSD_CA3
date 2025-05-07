@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-blue-100 flex justify-center items-center p-4" style="background-image: url('https://www.transparenttextures.com/patterns/diagmonds-light.png');">
        <!-- Outer Yellow Border Container -->
        <div class="w-full max-w-md relative border-4 border-yellow-300 rounded-lg shadow-lg">
            <!-- Main PC Container -->
            <div class="w-full relative">
                <!-- PC Top Section -->
                <div class="bg-gray-400 border-4 border-gray-700 rounded-t-lg p-2 mb-0">
                    <div class="bg-gray-300 border-2 border-gray-500 h-8 flex items-center px-2">
                        <div class="w-3 h-3 bg-red-500 rounded-full border border-gray-600 mr-1"></div>
                        <div class="w-3 h-3 bg-yellow-400 rounded-full border border-gray-600 mr-1"></div>
                        <div class="w-3 h-3 bg-green-500 rounded-full border border-gray-600"></div>
                    </div>
                </div>

                <!-- PC Screen Area -->
                <div class="bg-gray-200 border-8 border-gray-600 p-1">
                    <!-- Screen Bezel -->
                    <div class="bg-blue-900 border-4 border-gray-500 p-3">
                        <!-- Screen Content -->
                        <div class="bg-gray-100 border-2 border-gray-400 p-4 h-96 overflow-y-auto">
                            <!-- PC Menu Header -->
                            <div class="text-center mb-4">
                                <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/poke-ball.png"
                                     alt="Pokéball"
                                     class="h-10 w-10 mx-auto mb-2 pixel-art"
                                     style="image-rendering: pixelated;">
                                <h1 class="text-blue-800 text-sm tracking-wider font-press-start">TRAINER LOGIN SYSTEM</h1>
                                <div class="border-t-2 border-blue-800 my-2"></div>
                            </div>

                            <!-- Login Form -->
                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <!-- Pokémon Email -->
                                <div class="mb-4">
                                    <label for="email" class="block text-blue-800 text-xs mb-1 font-press-start">POKéMON MAIL:</label>
                                    <input id="email" type="email"
                                           class="w-full px-2 py-1 bg-white border-2 border-blue-700 rounded-sm text-gray-900 text-xs font-press-start @error('email') border-red-500 @enderror"
                                           name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                    @error('email')
                                    <p class="text-red-600 text-xxs mt-1 font-press-start">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Secret Code -->
                                <div class="mb-4">
                                    <label for="password" class="block text-blue-800 text-xs mb-1 font-press-start">ACCESS CODE:</label>
                                    <input id="password" type="password"
                                           class="w-full px-2 py-1 bg-white border-2 border-blue-700 rounded-sm text-gray-900 text-xs font-press-start @error('password') border-red-500 @enderror"
                                           name="password" required autocomplete="current-password">
                                    @error('password')
                                    <p class="text-red-600 text-xxs mt-1 font-press-start">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Remember Me -->
                                <div class="mb-4 flex items-center">
                                    <input type="checkbox" name="remember" id="remember"
                                           class="w-4 h-4 border-2 border-blue-700 rounded-sm focus:ring-blue-500 @error('remember') border-red-500 @enderror"
                                        {{ old('remember') ? 'checked' : '' }}>
                                    <label for="remember" class="ml-2 text-blue-800 text-xs font-press-start">
                                        REMEMBER THIS TRAINER
                                    </label>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex flex-col space-y-4 mt-6">
                                    <button type="submit"
                                            class="bg-red-600 hover:bg-red-700 text-white text-xs py-2 px-4 border-2 border-black shadow-sm font-press-start">
                                        LOGIN
                                    </button>

                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}"
                                           class="text-blue-600 hover:text-blue-800 text-xs text-center font-press-start">
                                            FORGOT ACCESS CODE?
                                        </a>
                                    @endif

                                    <a href="{{ route('register') }}"
                                       class="bg-blue-600 hover:bg-blue-700 text-white text-xs py-2 px-4 border-2 border-black shadow-sm font-press-start text-center">
                                        NEW TRAINER?
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- PC Bottom Section -->
                <div class="bg-gray-400 border-4 border-gray-700 rounded-b-lg p-2 mt-0">
                    <div class="flex justify-between items-center">
                        <div class="flex space-x-2">
                            <div class="w-8 h-8 bg-gray-300 border-2 border-gray-500 rounded-full"></div>
                            <div class="w-8 h-8 bg-gray-300 border-2 border-gray-500 rounded-full"></div>
                        </div>
                        <div class="text-xs font-press-start text-gray-700">POKéMON STORAGE SYSTEM v1.0</div>
                    </div>
                </div>

                <!-- Charmander Sprite -->
                <div class="absolute -bottom-4 -right-4">
                    <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/versions/generation-i/red-blue/4.png"
                         alt="Charmander"
                         class="h-16 w-16 pixel-art"
                         style="image-rendering: pixelated;">
                </div>
            </div>
        </div>
    </div>
@endsection
