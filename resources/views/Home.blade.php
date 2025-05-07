<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokémon Team Builder | Gen 1</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .pixel-art {
            image-rendering: pixelated;
        }
    </style>
</head>
<body class="bg-blue-100 min-h-screen" style="font-family: 'Press Start 2P', cursive;">
<!-- Header -->
<header class="bg-red-600 border-b-8 border-yellow-400 shadow-lg">
    <div class="container mx-auto px-4 py-6">
        <div class="flex flex-col md:flex-row items-center justify-between">
            <div class="flex items-center mb-4 md:mb-0">
                <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/poke-ball.png" alt="Pokéball" class="h-12 w-12 mr-4">
                <h1 class="text-yellow-400 text-2xl md:text-3xl text-shadow">POKÉMON TEAM BUILDER</h1>
            </div>
            <nav class="flex space-x-2 md:space-x-6">
                <a href="{{ route('home') }}" class="text-white hover:text-yellow-300 text-sm md:text-base">HOME</a>
                @auth
                    <a href="{{ route('builder') }}" class="text-white hover:text-yellow-300 text-sm md:text-base">BUILDER</a>
                @endauth
                <a href="{{ route('pokedex') }}" class="text-white hover:text-yellow-300 text-sm md:text-base">VIEW POKÉDEX</a>
                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-white hover:text-yellow-300 text-sm md:text-base">LOGOUT</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-white hover:text-yellow-300 text-sm md:text-base">LOGIN</a>
                    <a href="{{ route('register') }}" class="text-white hover:text-yellow-300 text-sm md:text-base">REGISTER</a>
                @endauth
            </nav>
        </div>
    </div>
</header>

<main class="container mx-auto px-4 py-8">
    <!-- Hero Section -->
    <div class="bg-white rounded-lg border-4 border-blue-800 p-6 mb-8">
        <div class="text-center mb-8">
            <h2 class="text-red-600 text-xl md:text-2xl mb-4">CREATE YOUR PERFECT GEN 1 TEAM</h2>
            <p class="text-gray-800 text-sm md:text-base mb-6">
                Build, share, and battle with teams from the original 151 Pokémon!
            </p>
            <a href="{{ route('builder') }}" class="bg-red-600 hover:bg-red-700 text-white py-3 px-6 rounded-lg inline-block border-2 border-black shadow-lg transform hover:scale-105 transition-transform">
                START BUILDING
            </a>
        </div>

        <!-- Featured Pokémon from Gen 1 using PokéAPI -->
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-8" id="featured-pokemon">
            @foreach($featuredPokemon as $pokemon)
                <div class="bg-gray-100 rounded-lg p-2 border-2 border-gray-300 hover:border-yellow-400 transition-colors text-center cursor-pointer"
                     onclick="window.location.href='/pokedex/{{ $pokemon->id }}'">
                    <img src="{{ $pokemon->sprite_url }}"
                         alt="{{ $pokemon->name }}"
                         class="w-full h-24 object-contain pixel-art">
                    <p class="text-xs mt-2 text-gray-800">{{ strtoupper($pokemon->name) }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Features Section -->
    <div class="grid md:grid-cols-3 gap-6 mb-8">
        <!-- Team Builder Card -->
        <div class="bg-gray-200 p-4 rounded-lg border-4 border-gray-400">
            <div class="bg-gray-300 p-2 mb-3 rounded border-2 border-gray-500">
                <h3 class="text-red-600 text-sm md:text-base mb-2">TEAM BUILDER</h3>
                <p class="text-gray-700 text-xs">Create teams with real PokéAPI data including stats, moves, and abilities.</p>
            </div>
            <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/6.png"
                 alt="Charizard"
                 class="w-full h-24 object-contain pixel-art rounded border-2 border-gray-400">
        </div>

        <!-- Pokédex Card -->
        <div class="bg-gray-200 p-4 rounded-lg border-4 border-gray-400">
            <div class="bg-gray-300 p-2 mb-3 rounded border-2 border-gray-500">
                <h3 class="text-red-600 text-sm md:text-base mb-2">POKÉDEX</h3>
                <p class="text-gray-700 text-xs">Explore all 151 Gen 1 Pokémon with detailed stats and moves.</p>
            </div>
            <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/9.png"
                 alt="Blastoise"
                 class="w-full h-24 object-contain pixel-art rounded border-2 border-gray-400">
        </div>

        <!-- Community Card -->
        <div class="bg-gray-200 p-4 rounded-lg border-4 border-gray-400">
            <div class="bg-gray-300 p-2 mb-3 rounded border-2 border-gray-500">
                <h3 class="text-red-600 text-sm md:text-base mb-2">COMMUNITY TEAMS</h3>
                <p class="text-gray-700 text-xs">Share your teams and discover builds from other trainers.</p>
            </div>
            <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/3.png"
                 alt="Venusaur"
                 class="w-full h-24 object-contain pixel-art rounded border-2 border-gray-400">
        </div>
    </div>

    <!-- Recent Teams Section -->
    <div class="bg-yellow-100 rounded-lg border-4 border-yellow-300 p-6">
        <h2 class="text-blue-800 text-lg md:text-xl mb-4">RECENTLY CREATED TEAMS</h2>
        <div class="grid md:grid-cols-3 gap-4" id="recent-teams">
            @foreach($exampleTeams as $team)
                <div class="bg-white p-4 rounded border-2 border-gray-300">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-red-600 text-sm">{{ $team['name'] }}</h3>
                        <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded">GEN 1</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach($team['pokemon'] as $pokemon)
                            <div class="text-center">
                                <img src="{{ $pokemon->sprite_url }}"
                                     alt="{{ $pokemon->name }}"
                                     class="w-full h-12 object-contain pixel-art">
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3 flex justify-between items-center">
                        <span class="text-gray-600 text-xs">BY {{ $team['user'] }}</span>
                        <a href="/teams/{{ $team['id'] ?? '1' }}" class="text-blue-600 hover:text-blue-800 text-xs">VIEW</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</main>

<!-- Footer -->
<footer class="bg-red-600 border-t-8 border-yellow-400 mt-12 py-6">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="mb-4 md:mb-0">
                <p class="text-white text-xs md:text-sm">© {{ date('Y') }} POKÉMON TEAM BUILDER | DATA FROM <a href="https://pokeapi.co/" class="text-yellow-300 hover:underline">POKÉAPI</a></p>
            </div>
            <div class="flex space-x-4">
                <a href="#" class="text-white hover:text-yellow-300 text-xs md:text-sm">TERMS</a>
                <a href="#" class="text-white hover:text-yellow-300 text-xs md:text-sm">PRIVACY</a>
                <a href="#" class="text-white hover:text-yellow-300 text-xs md:text-sm">CONTACT</a>
            </div>
        </div>
    </div>
</footer>

<script>
    // Fetch featured Pokémon (Gen 1 starters and popular Pokémon)
    document.addEventListener('DOMContentLoaded', function() {
        const featuredIds = [6, 9, 3, 25, 130, 65]; // Charizard, Blastoise, Venusaur, Pikachu, Gyarados, Alakazam

        const featuredContainer = document.getElementById('featured-pokemon');
        featuredContainer.innerHTML = '';

        featuredIds.forEach(id => {
            axios.get(`https://pokeapi.co/api/v2/pokemon/${id}`)
                .then(response => {
                    const pokemon = response.data;
                    const pokemonCard = document.createElement('div');
                    pokemonCard.className = 'bg-gray-100 rounded-lg p-2 border-2 border-gray-300 hover:border-yellow-400 transition-colors text-center';
                    pokemonCard.innerHTML = `
                        <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/${pokemon.id}.png"
                             alt="${pokemon.name}"
                             class="w-full h-24 object-contain pixel-art">
                        <p class="text-xs mt-2 text-gray-800">${pokemon.name.toUpperCase()}</p>
                    `;
                    pokemonCard.addEventListener('click', () => {
                        window.location.href = `/pokedex/${pokemon.id}`;
                    });
                    featuredContainer.appendChild(pokemonCard);
                })
                .catch(error => {
                    console.error('Error fetching Pokémon:', error);
                });
        });

        // Load recent teams (example - you would replace this with your actual data)
        const recentTeamsContainer = document.getElementById('recent-teams');
        recentTeamsContainer.innerHTML = '';

        // Example team data - in a real app you'd fetch this from your backend
        const exampleTeams = [
            { id: 1, name: 'KANTO STARTERS', pokemon: [1, 4, 7], user: 'ASH' },
            { id: 2, name: 'LEGENDARY TEAM', pokemon: [144, 145, 146], user: 'GARY' },
            { id: 3, name: 'POWER HOUSE', pokemon: [6, 9, 3, 149], user: 'RED' }
        ];

        exampleTeams.forEach(team => {
            const teamCard = document.createElement('div');
            teamCard.className = 'bg-white p-4 rounded border-2 border-gray-300';
            teamCard.innerHTML = `
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-red-600 text-sm">${team.name}</h3>
                    <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded">GEN 1</span>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    ${team.pokemon.map(pokemonId => `
                        <div class="text-center">
                            <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/${pokemonId}.png"
                                 alt="Pokémon"
                                 class="w-full h-12 object-contain pixel-art">
                        </div>
                    `).join('')}
                </div>
                <div class="mt-3 flex justify-between items-center">
                    <span class="text-gray-600 text-xs">BY ${team.user}</span>
                    <a href="/teams/${team.id}" class="text-blue-600 hover:text-blue-800 text-xs">VIEW</a>
                </div>
            `;
            recentTeamsContainer.appendChild(teamCard);
        });
    });
</script>
</body>
</html>
