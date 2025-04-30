@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-center">
            <div class="w-full max-w-md bg-white rounded-lg border-4 border-blue-800 p-6" style="font-family: 'Press Start 2P', cursive;">
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
                               class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg @error('email') border-red-500 @enderror"
                               name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="password" class="block text-gray-700 text-sm mb-2">SECRET CODE</label>
                        <input id="password" type="password"
                               class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg @error('password') border-red-500 @enderror"
                               name="password" required autocomplete="current-password">
                        @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <div class="flex items-center">
                            <input class="mr-2" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="text-gray-700 text-xs" for="remember">REMEMBER ME</label>
                        </div>
                    </div>

                    <div class="flex flex-col space-y-4 items-center">
                        <button type="submit"
                                class="w-full bg-red-600 hover:bg-red-700 text-white py-3 px-6 rounded-lg border-2 border-black shadow-lg transform hover:scale-105 transition-transform">
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
