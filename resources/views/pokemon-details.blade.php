<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pokemonId }} - Pokémon Details</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <style>
        /* Type Colors */
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

        /* Pokedex Matching Styles */
        .detail-card {
            background: #ffffff;
            border: 4px solid #3B4CCA;
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .move-item {
            border: 2px solid #3B4CCA;
            border-radius: 4px;
            padding: 0.5rem;
            margin: 0.25rem;
            text-align: center;
            font-size: 0.7rem;
            cursor: pointer;
            transition: all 0.2s ease;
            text-transform: capitalize;
        }

        .move-item:hover {
            transform: translateY(-2px);
            border-color: #FF0000;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .pixel-art {
            image-rendering: pixelated;
            width: 192px;
            height: 192px;
        }

        .loading-pokeball {
            animation: spin 1s linear infinite;
            width: 60px;
            height: 60px;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-blue-100 min-h-screen" style="font-family: 'Press Start 2P', cursive;">
<!-- Header -->
<header class="bg-red-600 border-b-8 border-yellow-400 shadow-lg">
    <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
            <a href="{{ route('pokedex') }}" class="flex items-center">
                <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/poke-ball.png"
                     alt="Pokéball"
                     class="h-12 w-12 mr-4">
                <span class="text-yellow-400 text-xl">BACK TO POKÉDEX</span>
            </a>
            <h1 class="text-yellow-400 text-xl md:text-2xl">POKÉMON DETAILS</h1>
        </div>
    </div>
</header>

<main class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg border-4 border-blue-800 p-4 shadow-lg">
        <!-- Loading State -->
        <div id="loading-state" class="text-center p-8">
            <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/poke-ball.png"
                 alt="Loading"
                 class="loading-pokeball mx-auto">
            <p class="text-gray-600 mt-4 text-sm">LOADING POKÉMON...</p>
        </div>

        <!-- Error State -->
        <div id="error-state" class="hidden text-center p-8">
            <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/54.png"
                 alt="Error"
                 class="w-24 h-24 mx-auto">
            <p class="text-red-600 mt-4 text-sm">ERROR LOADING POKÉMON!</p>
            <button onclick="window.location.reload()" class="bg-blue-500 text-white px-4 py-2 mt-4 rounded border-2 border-black">
                TRY AGAIN
            </button>
        </div>

        <!-- Success State -->
        <div id="success-state" class="hidden">
            <!-- Pokemon Header -->
            <div class="text-center mb-6">
                <div class="flex justify-center items-end mb-4">
                    <h2 id="pokemon-name" class="text-3xl text-gray-800 mr-4"></h2>
                    <span id="pokemon-id" class="text-gray-500 text-xl">#000</span>
                </div>
                <div id="pokemon-types" class="flex justify-center gap-2 mb-4"></div>
                <img id="pokemon-sprite"
                     src=""
                     alt=""
                     class="pixel-art mx-auto hover:scale-105 transition-transform cursor-pointer"
                     onclick="toggleSprite()">
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <!-- Basic Info -->
                <div class="detail-card">
                    <h3 class="text-gray-800 text-lg mb-4 border-b-2 border-gray-300 pb-2">BASIC INFO</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-gray-500 text-sm">HEIGHT</p>
                            <p id="pokemon-height" class="text-gray-800"></p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">WEIGHT</p>
                            <p id="pokemon-weight" class="text-gray-800"></p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">ABILITIES</p>
                            <ul id="pokemon-abilities" class="space-y-1"></ul>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="detail-card">
                    <h3 class="text-gray-800 text-lg mb-4 border-b-2 border-gray-300 pb-2">STATS</h3>
                    <div id="pokemon-stats" class="space-y-3"></div>
                </div>

                <!-- Evolutions -->
                <div class="detail-card">
                    <h3 class="text-gray-800 text-lg mb-4 border-b-2 border-gray-300 pb-2">EVOLUTIONS</h3>
                    <div id="pokemon-evolutions" class="flex justify-center items-center py-2"></div>
                </div>
            </div>

            <!-- Moves Section -->
            <div class="detail-card">
                <h3 class="text-gray-800 text-lg mb-4 border-b-2 border-gray-300 pb-2">MOVES</h3>
                <div id="pokemon-moves" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2"></div>
            </div>
        </div>
    </div>
</main>

<script>
    let currentPokemon = null;
    let isFrontSprite = true;
    const SPRITE_BASE = 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/';

    document.addEventListener('DOMContentLoaded', () => {
        loadPokemonData({{ $pokemonId }});
    });

    async function loadPokemonData(id) {
        try {
            showLoading();
            const response = await axios.get(`https://pokeapi.co/api/v2/pokemon/${id}`);
            currentPokemon = response.data;
            displayPokemonDetails(currentPokemon);
            hideLoading();
        } catch (error) {
            showError();
            console.error("Error loading Pokémon:", error);
        }
    }

    async function displayPokemonDetails(pokemon) {
        // Basic Info
        document.getElementById('pokemon-name').textContent = pokemon.name.toUpperCase();
        document.getElementById('pokemon-id').textContent = `#${pokemon.id.toString().padStart(3, '0')}`;
        document.getElementById('pokemon-height').textContent = `${pokemon.height / 10}m`;
        document.getElementById('pokemon-weight').textContent = `${pokemon.weight / 10}kg`;

        // Sprite
        const spriteImg = document.getElementById('pokemon-sprite');
        spriteImg.src = pokemon.sprites.front_default || `${SPRITE_BASE}${pokemon.id}.png`;
        spriteImg.onerror = () => spriteImg.src = `${SPRITE_BASE}0.png`;

        // Types
        const typesContainer = document.getElementById('pokemon-types');
        typesContainer.innerHTML = pokemon.types.map(type => `
            <span class="type-${type.type.name} px-3 py-1 rounded-full text-xs text-white">
                ${type.type.name.toUpperCase()}
            </span>
        `).join('');

        // Abilities
        const abilitiesContainer = document.getElementById('pokemon-abilities');
        abilitiesContainer.innerHTML = pokemon.abilities.map(ability => `
            <li class="text-sm">
                ${ability.ability.name.replace('-', ' ')}
                ${ability.is_hidden ? '<span class="text-gray-500 text-xs">(hidden)</span>' : ''}
            </li>
        `).join('');

        // Stats
        const statsContainer = document.getElementById('pokemon-stats');
        statsContainer.innerHTML = pokemon.stats.map(stat => `
            <div class="text-sm">
                <div class="flex justify-between mb-1">
                    <span>${stat.stat.name.toUpperCase()}</span>
                    <span>${stat.base_stat}</span>
                </div>
                <div class="h-2 bg-gray-200 rounded-full">
                    <div class="h-2 rounded-full bg-blue-500"
                         style="width: ${Math.min(100, stat.base_stat)}%"></div>
                </div>
            </div>
        `).join('');

        // Moves with Type Colors
        const movesContainer = document.getElementById('pokemon-moves');
        movesContainer.innerHTML = '';
        const moves = pokemon.moves.slice(0, 30);

        for (const move of moves) {
            const moveElement = document.createElement('div');
            moveElement.className = 'move-item';
            moveElement.textContent = move.move.name.replace('-', ' ');

            try {
                const moveResponse = await axios.get(move.move.url);
                const moveType = moveResponse.data.type.name;
                moveElement.classList.add(`type-${moveType}`);

                // Store original color for hover effect
                const originalColor = getComputedStyle(moveElement).backgroundColor;
                moveElement.dataset.originalColor = originalColor;

                // Hover effects
                moveElement.addEventListener('mouseover', () => {
                    moveElement.style.backgroundColor = shadeColor(originalColor, -20);
                });
                moveElement.addEventListener('mouseout', () => {
                    moveElement.style.backgroundColor = originalColor;
                });
            } catch (error) {
                moveElement.classList.add('type-normal');
            }

            movesContainer.appendChild(moveElement);
        }

        // Evolutions
        loadEvolutionChain(pokemon.species.url);
    }

    async function loadEvolutionChain(speciesUrl) {
        try {
            const speciesResponse = await axios.get(speciesUrl);
            const evolutionResponse = await axios.get(speciesResponse.data.evolution_chain.url);
            const evolutions = getEvolutionChain(evolutionResponse.data.chain);

            const evoContainer = document.getElementById('pokemon-evolutions');
            evoContainer.innerHTML = evolutions.map((evo, index) => `
                <div class="inline-block mx-2 ${evo.name === currentPokemon.name ? 'scale-110' : 'opacity-75'}">
                    <img src="${SPRITE_BASE}${evo.id}.png"
                         alt="${evo.name}"
                         class="w-16 h-16 mx-auto pixel-art">
                    <p class="text-xs mt-1">${evo.name.toUpperCase()}</p>
                    ${index < evolutions.length - 1 ? '<span class="mx-2">➔</span>' : ''}
                </div>
            `).join('');
        } catch (error) {
            document.getElementById('pokemon-evolutions').innerHTML =
                '<p class="text-gray-500 text-sm">EVOLUTION DATA UNAVAILABLE</p>';
        }
    }

    // Helper functions
    function getEvolutionChain(chain) {
        const evolutions = [];
        let currentChain = chain;
        while (currentChain) {
            evolutions.push({
                name: currentChain.species.name,
                id: currentChain.species.url.match(/\/pokemon-species\/(\d+)/)[1]
            });
            currentChain = currentChain.evolves_to[0];
        }
        return evolutions;
    }

    function shadeColor(color, percent) {
        const num = parseInt(color.replace('#',''), 16);
        const amt = Math.round(2.55 * percent);
        const R = (num >> 16) + amt;
        const G = (num >> 8 & 0x00FF) + amt;
        const B = (num & 0x0000FF) + amt;
        return `#${(1 << 24 | (R < 255 ? R < 1 ? 0 : R : 255) << 16 | (G < 255 ? G < 1 ? 0 : G : 255) << 8 | (B < 255 ? B < 1 ? 0 : B : 255)).toString(16).slice(1)}`;
    }

    function toggleSprite() {
        if (!currentPokemon) return;
        isFrontSprite = !isFrontSprite;
        const sprite = document.getElementById('pokemon-sprite');
        sprite.src = isFrontSprite
            ? currentPokemon.sprites.front_default
            : currentPokemon.sprites.back_default;
    }

    function showLoading() {
        document.getElementById('loading-state').classList.remove('hidden');
        document.getElementById('error-state').classList.add('hidden');
        document.getElementById('success-state').classList.add('hidden');
    }

    function hideLoading() {
        document.getElementById('loading-state').classList.add('hidden');
        document.getElementById('success-state').classList.remove('hidden');
    }

    function showError() {
        document.getElementById('loading-state').classList.add('hidden');
        document.getElementById('error-state').classList.remove('hidden');
    }
</script>
</body>
</html>
