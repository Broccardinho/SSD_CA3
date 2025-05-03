<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pokemonId }} - Pokémon Details</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom styles for Pokémon types */
        .type-normal { background-color: #A8A878; }
        .type-fire { background-color: #F08030; }
        .type-water { background-color: #6890F0; }
        .type-electric { background-color: #F8D030; }
        .type-grass { background-color: #78C850; }
        .type-ice { background-color: #98D8D8; }
        .type-fighting { background-color: #C03028; }
        .type-poison { background-color: #A040A0; }
        .type-ground { background-color: #E0C068; }
        .type-flying { background-color: #A890F0; }
        .type-psychic { background-color: #F85888; }
        .type-bug { background-color: #A8B820; }
        .type-rock { background-color: #B8A038; }
        .type-ghost { background-color: #705898; }
        .type-dragon { background-color: #7038F8; }
        .type-dark { background-color: #705848; }
        .type-steel { background-color: #B8B8D0; }
        .type-fairy { background-color: #EE99AC; }

        /* Pixel art rendering */
        .pixel-art {
            image-rendering: pixelated;
            image-rendering: -moz-crisp-edges;
            image-rendering: crisp-edges;
        }

        /* Stats bar colors */
        .stat-hp { background-color: #FF5959; }
        .stat-attack { background-color: #F5AC78; }
        .stat-defense { background-color: #FAE078; }
        .stat-special-attack { background-color: #9DB7F5; }
        .stat-special-defense { background-color: #A7DB8D; }
        .stat-speed { background-color: #FA92B2; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
<!-- Header with back button -->
<header class="bg-red-600 border-b-8 border-yellow-400 shadow-lg">
    <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
            <a href="{{ route('pokedex') }}" class="flex items-center">
                <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/poke-ball.png"
                     alt="Pokéball"
                     class="h-8 w-8 mr-2">
                <span class="text-white font-bold">BACK TO POKÉDEX</span>
            </a>
            <h1 class="text-yellow-300 text-xl md:text-2xl">POKÉMON DETAILS</h1>
        </div>
    </div>
</header>

<!-- Main content area -->
<main class="container mx-auto px-4 py-8">
    <div id="pokemon-details" class="bg-white rounded-xl border-4 border-blue-800 shadow-lg overflow-hidden">
        <!-- Loading state -->
        <div id="loading-state" class="p-8 text-center">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-red-600 mb-4"></div>
            <p class="text-gray-700">Loading Pokémon data...</p>
        </div>

        <!-- Error state (hidden by default) -->
        <div id="error-state" class="hidden p-8 text-center bg-red-50">
            <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/54.png"
                 alt="Psyduck confused"
                 class="w-24 h-24 mx-auto mb-4">
            <h3 class="text-xl font-bold text-red-600 mb-2">Oops! Something went wrong</h3>
            <p class="text-gray-700 mb-4">We couldn't load the Pokémon data. Please try again later.</p>
            <a href="{{ route('pokedex') }}" class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors">
                Return to Pokédex
            </a>
        </div>

        <!-- Success state (hidden by default) -->
        <div id="success-state" class="hidden">
            <!-- Pokémon Header -->
            <div class="bg-gradient-to-r from-red-600 to-red-500 p-6 text-center">
                <div class="flex justify-center items-end mb-2">
                    <h2 id="pokemon-name" class="text-3xl font-bold text-white mr-4 capitalize"></h2>
                    <span id="pokemon-id" class="text-yellow-300 text-xl font-mono"></span>
                </div>
                <div id="pokemon-types" class="flex justify-center gap-2 mb-4"></div>
                <img id="pokemon-sprite"
                     src=""
                     alt=""
                     class="w-48 h-48 mx-auto pixel-art hover:scale-110 transition-transform cursor-pointer"
                     onclick="toggleSprite()">
            </div>

            <!-- Pokémon Details -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">
                <!-- Left Column - Basic Info -->
                <div class="bg-gray-50 p-4 rounded-lg border-2 border-gray-200">
                    <h3 class="text-lg font-bold mb-4 text-center border-b-2 border-gray-300 pb-2">BASIC INFO</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-500">Height</p>
                            <p id="pokemon-height" class="font-bold"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Weight</p>
                            <p id="pokemon-weight" class="font-bold"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Abilities</p>
                            <ul id="pokemon-abilities" class="space-y-1"></ul>
                        </div>
                    </div>
                </div>

                <!-- Middle Column - Stats -->
                <div class="bg-gray-50 p-4 rounded-lg border-2 border-gray-200">
                    <h3 class="text-lg font-bold mb-4 text-center border-b-2 border-gray-300 pb-2">STATS</h3>
                    <div id="pokemon-stats" class="space-y-3"></div>
                </div>

                <!-- Right Column - Evolutions -->
                <div class="bg-gray-50 p-4 rounded-lg border-2 border-gray-200">
                    <h3 class="text-lg font-bold mb-4 text-center border-b-2 border-gray-300 pb-2">EVOLUTIONS</h3>
                    <div id="pokemon-evolutions" class="text-center py-4">
                        <p class="text-gray-500">Loading evolution data...</p>
                    </div>
                </div>
            </div>

            <!-- Moves Section -->
            <div class="border-t-2 border-gray-200 p-6">
                <h3 class="text-xl font-bold mb-4 text-center">MOVES</h3>
                <div id="pokemon-moves" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3"></div>
            </div>
        </div>
    </div>
</main>

<script>
    // Global variables
    let currentPokemon = null;
    let isFrontSprite = true;
    let currentSpriteUrl = '';
    let backSpriteUrl = '';

    document.addEventListener('DOMContentLoaded', function() {
        const pokemonId = {{ $pokemonId }};
        loadPokemonData(pokemonId);
    });

    async function loadPokemonData(pokemonId) {
        try {
            // Show loading state
            document.getElementById('loading-state').classList.remove('hidden');
            document.getElementById('error-state').classList.add('hidden');
            document.getElementById('success-state').classList.add('hidden');

            // Fetch Pokémon data
            const pokemonResponse = await axios.get(`https://pokeapi.co/api/v2/pokemon/${pokemonId}`);
            currentPokemon = pokemonResponse.data;

            // Fetch species data for evolution chain
            const speciesResponse = await axios.get(`https://pokeapi.co/api/v2/pokemon-species/${pokemonId}`);
            const speciesData = speciesResponse.data;

            // Set sprite URLs
            currentSpriteUrl = currentPokemon.sprites.front_default;
            backSpriteUrl = currentPokemon.sprites.back_default || currentSpriteUrl;

            // Display the data
            displayPokemonDetails(currentPokemon, speciesData);

            // Hide loading state, show success state
            document.getElementById('loading-state').classList.add('hidden');
            document.getElementById('success-state').classList.remove('hidden');

        } catch (error) {
            console.error("Error loading Pokémon data:", error);
            document.getElementById('loading-state').classList.add('hidden');
            document.getElementById('error-state').classList.remove('hidden');
        }
    }

    function displayPokemonDetails(pokemon, speciesData) {
        // Basic info
        document.getElementById('pokemon-name').textContent = pokemon.name;
        document.getElementById('pokemon-id').textContent = `#${pokemon.id.toString().padStart(3, '0')}`;
        document.getElementById('pokemon-sprite').src = currentSpriteUrl;
        document.getElementById('pokemon-sprite').alt = pokemon.name;

        // Height and weight (convert from decimeters/hectograms)
        document.getElementById('pokemon-height').textContent = `${(pokemon.height / 10).toFixed(2)} m`;
        document.getElementById('pokemon-weight').textContent = `${(pokemon.weight / 10).toFixed(2)} kg`;

        // Types
        const typesContainer = document.getElementById('pokemon-types');
        typesContainer.innerHTML = '';
        pokemon.types.forEach(type => {
            const typeElement = document.createElement('span');
            typeElement.className = `px-4 py-1 rounded-full text-white font-bold type-${type.type.name}`;
            typeElement.textContent = type.type.name.toUpperCase();
            typesContainer.appendChild(typeElement);
        });

        // Abilities
        const abilitiesContainer = document.getElementById('pokemon-abilities');
        abilitiesContainer.innerHTML = '';
        pokemon.abilities.forEach(ability => {
            const abilityElement = document.createElement('li');
            abilityElement.className = 'capitalize';
            abilityElement.textContent = ability.ability.name.replace('-', ' ');
            if (ability.is_hidden) {
                abilityElement.innerHTML += ' <span class="text-xs text-gray-500">(hidden)</span>';
            }
            abilitiesContainer.appendChild(abilityElement);
        });

        // Stats
        const statsContainer = document.getElementById('pokemon-stats');
        statsContainer.innerHTML = '';
        pokemon.stats.forEach(stat => {
            const statName = stat.stat.name.replace('-', ' ');
            const statValue = stat.base_stat;

            const statElement = document.createElement('div');
            statElement.innerHTML = `
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-medium capitalize">${statName}</span>
                        <span class="text-sm font-bold">${statValue}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="stat-${statName.split(' ')[0]} h-2 rounded-full"
                             style="width: ${Math.min(100, statValue)}%"></div>
                    </div>
                `;
            statsContainer.appendChild(statElement);
        });

        // Moves
        const movesContainer = document.getElementById('pokemon-moves');
        movesContainer.innerHTML = '';
        pokemon.moves.slice(0, 20).forEach(move => { // Limit to first 20 moves
            const moveElement = document.createElement('div');
            moveElement.className = 'bg-gray-100 hover:bg-gray-200 p-2 rounded text-center cursor-pointer transition-colors';
            moveElement.textContent = move.move.name.replace('-', ' ');
            movesContainer.appendChild(moveElement);
        });

        // Load evolution chain if available
        if (speciesData.evolution_chain) {
            loadEvolutionChain(speciesData.evolution_chain.url);
        }
    }

    async function loadEvolutionChain(url) {
        try {
            const response = await axios.get(url);
            const chain = response.data.chain;
            const evolutionsContainer = document.getElementById('pokemon-evolutions');

            // Simple display - you can expand this to show the full chain
            const speciesNames = [];
            let currentChain = chain;

            while (currentChain) {
                speciesNames.push(currentChain.species.name);
                currentChain = currentChain.evolves_to[0];
            }

            evolutionsContainer.innerHTML = '';
            speciesNames.forEach((name, index) => {
                const pokemonId = name.split('-').pop(); // Simple way to get ID
                const evolutionElement = document.createElement('div');
                evolutionElement.className = `inline-block mx-2 ${name === currentPokemon.name ? 'scale-110' : 'opacity-70'}`;
                evolutionElement.innerHTML = `
                        <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/${pokemonId}.png"
                             alt="${name}"
                             class="w-16 h-16 mx-auto pixel-art">
                        <p class="text-sm capitalize">${name}</p>
                        ${index < speciesNames.length - 1 ? '<span class="text-lg">→</span>' : ''}
                    `;
                evolutionsContainer.appendChild(evolutionElement);
            });

        } catch (error) {
            console.error("Error loading evolution chain:", error);
            document.getElementById('pokemon-evolutions').innerHTML =
                '<p class="text-gray-500">Evolution data not available</p>';
        }
    }

    function toggleSprite() {
        isFrontSprite = !isFrontSprite;
        document.getElementById('pokemon-sprite').src = isFrontSprite ? currentSpriteUrl : backSpriteUrl;
    }
</script>
</body>
</html>
