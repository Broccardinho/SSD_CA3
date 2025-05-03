<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gen 1 Pokédex</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        .pokemon-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 1rem;
            padding: 1rem;
        }
        .pokemon-card {
            background: white;
            border: 2px solid #ccc;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .pokemon-card:hover {
            transform: scale(1.05);
            border-color: #ff0000;
        }
        .pokemon-sprite {
            image-rendering: pixelated;
            width: 72px;
            height: 72px;
        }
    </style>
</head>
<body class="bg-gray-100">
<header class="bg-red-600 text-white p-4 shadow-md">
    <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-2xl font-bold">GEN 1 POKÉDEX</h1>
        <a href="{{ route('home') }}" class="bg-blue-500 px-4 py-2 rounded">Back to Home</a>
    </div>
</header>

<main class="container mx-auto p-4">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <!-- Search Box -->
        <div class="mb-6">
            <input type="text" id="search" placeholder="Search Pokémon..."
                   class="w-full p-2 border border-gray-300 rounded">
        </div>

        <!-- Pokémon Grid -->
        <div class="pokemon-grid" id="pokemon-container">
            <!-- JavaScript will populate this -->
            <div class="text-center py-10">Loading Pokémon...</div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('pokemon-container');
        const searchInput = document.getElementById('search');

        // Fetch all Gen 1 Pokémon (IDs 1-151)
        async function fetchPokemon() {
            container.innerHTML = '<div class="text-center py-10">Loading Pokémon...</div>';

            try {
                const pokemonList = [];

                // Fetch first 151 Pokémon (Gen 1)
                for (let id = 1; id <= 151; id++) {
                    const response = await axios.get(`https://pokeapi.co/api/v2/pokemon/${id}`);
                    pokemonList.push(response.data);
                }

                displayPokemon(pokemonList);
            } catch (error) {
                container.innerHTML = '<div class="text-red-500 text-center py-10">Failed to load Pokémon. Try refreshing.</div>';
                console.error("Error fetching Pokémon:", error);
            }
        }

        // Display Pokémon in grid
        function displayPokemon(pokemonList) {
            container.innerHTML = '';

            pokemonList.forEach(pokemon => {
                const card = document.createElement('div');
                card.className = 'pokemon-card';
                card.innerHTML = `
                        <img src="${pokemon.sprites.front_default}"
                             alt="${pokemon.name}"
                             class="pokemon-sprite mx-auto">
                        <p class="mt-2 font-semibold">${pokemon.name.toUpperCase()}</p>
                        <p class="text-sm text-gray-600">#${pokemon.id.toString().padStart(3, '0')}</p>
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
