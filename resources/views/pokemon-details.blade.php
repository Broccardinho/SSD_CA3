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
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 360px; /* Standard width for Basic Info */
            margin: 0 auto;
        }

        .stats-card {
            max-width: 400px; /* Slightly wider for Stats */
        }

        .evolutions-card {
            max-width: 420px; /* Wider for Evolutions to prevent compression */
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

        .shiny-container {
            position: relative;
            display: inline-block;
        }

        .shiny-badge {
            position: absolute;
            top: -10px;
            right: -10px;
            background: #FFD700;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.6rem;
            color: #000;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            transform: rotate(15deg);
        }

        .weakness-item {
            display: inline-block;
            margin: 2px;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            text-transform: uppercase;
            border: 2px solid transparent;
            color: #fff;
        }

        .effectiveness-4 { background-color: #FF4444; border-color: #FF0000; }
        .effectiveness-2 { background-color: #FF8888; }
        .effectiveness-1 { background-color: #A0A0A0; }
        .effectiveness-0_5 { background-color: #88CC88; }
        .effectiveness-0_25 { background-color: #44AA44; border-color: #00FF00; }
        .effectiveness-0 { background-color: #666666; }

        .loading-pokeball {
            animation: spin 1s linear infinite;
            width: 60px;
            height: 60px;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .sprite-container {
            max-width: 400px;
            margin: 0 auto;
        }

        .stat-item {
            width: 100%;
            text-align: left;
            padding: 0 0.5rem;
        }

        /* Evolution chain styling */
        .evolution-item {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            min-width: 100px; /* Prevent compression */
            margin: 0 0.5rem;
        }

        /* Layout for detail cards */
        .details-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.5rem; /* Near-touching boxes */
            margin-bottom: 1.5rem;
        }

        .full-width-card {
            width: 100%;
            max-width: 95%; /* Nearly full width of parent */
            margin: 0 auto;
        }

        /* Parent container */
        .main-container {
            padding: 1rem; /* Minimal padding for full-width cards */
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
    <div class="main-container bg-white rounded-lg border-4 border-blue-800 shadow-lg">
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
                <div class="sprite-container flex justify-center gap-2">
                    <img id="pokemon-sprite"
                         src=""
                         alt=""
                         class="pixel-art mx-auto hover:scale-105 transition-transform cursor-pointer"
                         onclick="toggleSprite()">
                    <div class="shiny-container">
                        <img id="pokemon-shiny"
                             src=""
                             alt="Shiny"
                             class="pixel-art mx-auto hover:scale-105 transition-transform cursor-pointer"
                             onclick="toggleSprite()">
                        <div id="shiny-badge" class="shiny-badge hidden">SHINY!</div>
                    </div>
                </div>
            </div>

            <!-- Details Row (Basic Info, Stats, Evolutions) -->
            <div class="details-row">
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
                <div class="detail-card stats-card">
                    <h3 class="text-gray-800 text-lg mb-4 border-b-2 border-gray-300 pb-2">STATS</h3>
                    <div id="pokemon-stats" class="space-y-2"></div>
                </div>

                <!-- Evolutions -->
                <div class="detail-card evolutions-card">
                    <h3 class="text-gray-800 text-lg mb-4 border-b-2 border-gray-300 pb-2">EVOLUTIONS</h3>
                    <div id="pokemon-evolutions" class="flex justify-center items-center py-2"></div>
                </div>
            </div>

            <!-- Type Weaknesses -->
            <div class="detail-card full-width-card mb-6">
                <h3 class="text-gray-800 text-lg mb-4 border-b-2 border-gray-300 pb-2">TYPE WEAKNESSES</h3>
                <div id="pokemon-weaknesses" class="flex flex-wrap justify-center gap-1"></div>
            </div>

            <!-- Moves Section -->
            <div class="detail-card full-width-card">
                <h3 class="text-gray-800 text-lg mb-4 border-b-2 border-gray-300 pb-2">MOVES</h3>
                <div id="pokemon-moves" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2"></div>
            </div>
        </div>
    </div>
</main>

<script>
    let currentPokemon = null;
    let isFrontSprite = true;
    let isShiny = false;
    const SPRITE_BASE = 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/';

    document.addEventListener('DOMContentLoaded', () => {
        loadPokemonData({{ $pokemonId }});
    });

    async function loadPokemonData(id) {
        try {
            showLoading();
            const response = await axios.get(`https://pokeapi.co/api/v2/pokemon/${id}`);
            currentPokemon = response.data;
            await displayPokemonDetails(currentPokemon);
            hideLoading();
        } catch (error) {
            console.error("Error loading Pokémon:", error);
            showError();
        }
    }

    async function displayPokemonDetails(pokemon) {
        // Basic Info
        document.getElementById('pokemon-name').textContent = pokemon.name.toUpperCase();
        document.getElementById('pokemon-id').textContent = `#${pokemon.id.toString().padStart(3, '0')}`;
        document.getElementById('pokemon-height').textContent = `${pokemon.height / 10}m`;
        document.getElementById('pokemon-weight').textContent = `${pokemon.weight / 10}kg`;

        // Sprites
        const spriteImg = document.getElementById('pokemon-sprite');
        const shinyImg = document.getElementById('pokemon-shiny');
        if (!spriteImg || !shinyImg) {
            console.error("Sprite elements not found in DOM");
            showError();
            return;
        }

        spriteImg.src = pokemon.sprites.front_default || `${SPRITE_BASE}${pokemon.id}.png`;
        shinyImg.src = pokemon.sprites.front_shiny || pokemon.sprites.front_default || `${SPRITE_BASE}${pokemon.id}.png`;
        shinyImg.style.opacity = pokemon.sprites.front_shiny ? '1' : '0.3';
        document.getElementById('shiny-badge').classList.toggle('hidden', !isShiny || !pokemon.sprites.front_shiny);
        spriteImg.onerror = () => spriteImg.src = `${SPRITE_BASE}0.png`;
        shinyImg.onerror = () => shinyImg.src = `${SPRITE_BASE}0.png`;

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
            <div class="stat-item text-sm">
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
                    moveElement.style.backgroundColor = shadeColor(originalColor, 20);
                });
                moveElement.addEventListener('mouseout', () => {
                    moveElement.style.backgroundColor = originalColor;
                });
            } catch (error) {
                moveElement.classList.add('type-normal');
            }

            movesContainer.appendChild(moveElement);
        }

        // Weaknesses
        const weaknesses = await calculateWeaknesses(pokemon.types);
        displayWeaknesses(weaknesses);

        // Evolutions
        loadEvolutionChain(pokemon.species.url);
    }

    async function calculateWeaknesses(types) {
        const typeRelations = {};
        try {
            for (const typeData of types) {
                const typeResponse = await axios.get(typeData.type.url);
                const relations = typeResponse.data.damage_relations;
                relations.double_damage_from.forEach(t => {
                    typeRelations[t.name] = (typeRelations[t.name] || 1) * 2;
                });
                relations.half_damage_from.forEach(t => {
                    typeRelations[t.name] = (typeRelations[t.name] || 1) * 0.5;
                });
                relations.no_damage_from.forEach(t => {
                    typeRelations[t.name] = 0;
                });
            }
        } catch (error) {
            console.error("Error fetching type data:", error);
            return {};
        }
        return Object.entries(typeRelations).reduce((acc, [type, multiplier]) => {
            const key = multiplier.toFixed(2);
            acc[key] = acc[key] || [];
            acc[key].push(type);
            return acc;
        }, {});
    }

    function displayWeaknesses(weaknesses) {
        const container = document.getElementById('pokemon-weaknesses');
        container.innerHTML = '';
        Object.entries(weaknesses).sort((a, b) => b[0] - a[0]).forEach(([multiplier, types]) => {
            const effectivenessClass = getEffectivenessClass(parseFloat(multiplier));
            types.forEach(type => {
                const element = document.createElement('span');
                element.className = `weakness-item ${effectivenessClass}`;
                element.textContent = type;
                element.title = `${multiplier}x effectiveness`;
                container.appendChild(element);
            });
        });
    }

    function getEffectivenessClass(multiplier) {
        if (multiplier === 4) return 'effectiveness-4';
        if (multiplier === 2) return 'effectiveness-2';
        if (multiplier === 1) return 'effectiveness-1';
        if (multiplier === 0.5) return 'effectiveness-0_5';
        if (multiplier === 0.25) return 'effectiveness-0_25';
        if (multiplier === 0) return 'effectiveness-0';
        return '';
    }

    async function loadEvolutionChain(speciesUrl) {
        try {
            const speciesResponse = await axios.get(speciesUrl);
            const evolutionResponse = await axios.get(speciesResponse.data.evolution_chain.url);
            const evolutions = getEvolutionChain(evolutionResponse.data.chain);
            const evoContainer = document.getElementById('pokemon-evolutions');
            evoContainer.innerHTML = evolutions.map((evo, index) => `
                <div class="evolution-item ${evo.name === currentPokemon.name ? 'scale-110' : 'opacity-75'}">
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
        let R, G, B;
        if (color.startsWith('rgb')) {
            const match = color.match(/rgb\((\d+),\s*(\d+),\s*(\d+)\)/);
            if (!match) return color;
            R = parseInt(match[1]);
            G = parseInt(match[2]);
            B = parseInt(match[3]);
        } else {
            const num = parseInt(color.replace('#', ''), 16);
            R = (num >> 16) & 255;
            G = (num >> 8) & 255;
            B = num & 255;
        }
        const amt = Math.round(2.55 * percent);
        R = Math.min(255, Math.max(0, R + amt));
        G = Math.min(255, Math.max(0, G + amt));
        B = Math.min(255, Math.max(0, B + amt));
        return `rgb(${R}, ${G}, ${B})`;
    }

    function toggleSprite() {
        if (!currentPokemon) return;
        isFrontSprite = !isFrontSprite;
        const sprite = document.getElementById('pokemon-sprite');
        const shiny = document.getElementById('pokemon-shiny');
        if (!sprite || !shiny) {
            console.error("Sprite elements not found in DOM during toggle");
            return;
        }
        sprite.src = isFrontSprite
            ? (isShiny ? (currentPokemon.sprites.front_shiny || currentPokemon.sprites.front_default) : currentPokemon.sprites.front_default || `${SPRITE_BASE}${currentPokemon.id}.png`)
            : (isShiny ? (currentPokemon.sprites.back_shiny || currentPokemon.sprites.back_default) : currentPokemon.sprites.back_default || `${SPRITE_BASE}${currentPokemon.id}.png`);
        shiny.src = isFrontSprite
            ? (isShiny ? currentPokemon.sprites.front_default : (currentPokemon.sprites.front_shiny || currentPokemon.sprites.front_default) || `${SPRITE_BASE}${currentPokemon.id}.png`)
            : (isShiny ? currentPokemon.sprites.back_default : (currentPokemon.sprites.back_shiny || currentPokemon.sprites.back_default) || `${SPRITE_BASE}${currentPokemon.id}.png`);
        shiny.style.opacity = currentPokemon.sprites[isShiny ? 'front_default' : 'front_shiny'] ? '1' : '0.3';
        document.getElementById('shiny-badge').classList.toggle('hidden', !isShiny || !currentPokemon.sprites[isShiny ? 'front_default' : 'front_shiny']);
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
