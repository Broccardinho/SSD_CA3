<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gen 1 Pokédex</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        .pokedex-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 1rem;
            padding: 1rem;
        }

        .pokemon-card {
            background: #ffffff;
            border: 4px solid #3B4CCA;
            border-radius: 8px;
            padding: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .pokemon-card:hover {
            transform: translateY(-2px);
            border-color: #FF0000;
            box-shadow: 0 6px 12px rgba(0,0,0,0.2);
        }

        .pixel-sprite {
            image-rendering: pixelated;
            width: 80px;
            height: 80px;
            margin: 0 auto;
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
            <div class="flex items-center">
                <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/poke-ball.png"
                     alt="Pokéball"
                     class="h-12 w-12 mr-4">
                <h1 class="text-yellow-400 text-xl md:text-2xl">GEN 1 POKÉDEX</h1>
            </div>
            <a href="{{ route('home') }}" class="bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded-lg border-2 border-black shadow-lg">
                BACK HOME
            </a>
        </div>
    </div>
</header>

<main class="container mx-auto px-4 py-8">
    <!-- Search Box -->
    <div class="bg-white rounded-lg border-4 border-blue-800 p-4 mb-8">
        <input type="text"
               id="search"
               placeholder="SEARCH POKÉMON..."
               class="w-full p-3 border-2 border-red-600 rounded-lg text-sm focus:outline-none focus:border-yellow-400"
               style="font-family: 'Press Start 2P', cursive;">
    </div>

    <!-- Pokémon Grid -->
    <div class="bg-white rounded-lg border-4 border-blue-800 p-4">
        <div class="pokedex-grid" id="pokemon-container">
            <!-- Loading State -->
            <div class="col-span-full text-center py-10">
                <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/poke-ball.png"
                     alt="Loading"
                     class="loading-pokeball mx-auto">
                <p class="text-gray-600 mt-4 text-sm">LOADING POKÉMON...</p>
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('pokemon-container');
        const searchInput = document.getElementById('search');

        // Fetch all Gen 1 Pokémon (IDs 1-151)
        async function fetchPokemon() {
            try {
                const pokemonList = [];

                // Fetch first 151 Pokémon (Gen 1)
                for (let id = 1; id <= 151; id++) {
                    const response = await axios.get(`https://pokeapi.co/api/v2/pokemon/${id}`);
                    pokemonList.push(response.data);
                }

                displayPokemon(pokemonList);
            } catch (error) {
                container.innerHTML = `
                        <div class="col-span-full text-center py-10">
                            <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/54.png"
                                 alt="Error"
                                 class="w-16 h-16 mx-auto">
                            <p class="text-red-600 mt-4 text-sm">ERROR LOADING POKÉMON!</p>
                        </div>`;
                console.error("Error fetching Pokémon:", error);
            }
        }

        // Display Pokémon in grid
        function displayPokemon(pokemonList) {
            container.innerHTML = '';

            pokemonList.sort((a, b) => a.id - b.id).forEach(pokemon => {
                const card = document.createElement('div');
                card.className = 'pokemon-card';
                card.innerHTML = `
                        <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/${pokemon.id}.png"
                             alt="${pokemon.name}"
                             class="pixel-sprite">
                        <p class="text-gray-800 text-xs mt-2">${pokemon.name.toUpperCase()}</p>
                        <p class="text-gray-500 text-xxs">#${pokemon.id.toString().padStart(3, '0')}</p>
                    `;
                card.addEventListener('click', () => {
                    window.location.href = "{{ route('pokemon.details', '') }}/" + pokemon.id;
                });
                container.appendChild(card);
            });
        }

        // Search functionality
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const cards = document.querySelectorAll('.pokemon-card');

            cards.forEach(card => {
                const name = card.querySelector('p').textContent.toLowerCase();
                card.style.display = name.includes(searchTerm) ? 'block' : 'none';
            });
        });

        // Initial load
        fetchPokemon();
    });
</script>
</body>
</html>
