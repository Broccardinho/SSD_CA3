@extends('layouts.app')

@section('content')

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Pokémon Team Builder | Gen 1</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
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
    <div class="bg-blue-100 min-h-screen" style="font-family: 'Press Start 2P', cursive;">
        <!-- Header Matching Home Menu -->
        <header class="bg-red-600 border-b-8 border-yellow-400 shadow-lg">
            <div class="container mx-auto px-4 py-6">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="flex items-center mb-4 md:mb-0">
                        <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/poke-ball.png"
                             alt="Pokéball"
                             class="h-12 w-12 mr-4">
                        <h1 class="text-yellow-400 text-2xl md:text-3xl text-shadow">POKÉMON TEAM BUILDER</h1>
                    </div>
                    <nav class="flex space-x-2 md:space-x-6">
                        <a href="{{ route('home') }}" class="text-white hover:text-yellow-300 text-sm md:text-base">HOME</a>
                        <a href="{{ route('pokedex') }}" class="text-white hover:text-yellow-300 text-sm md:text-base">POKÉDEX</a>
                    </nav>
                </div>
            </div>
        </header>

        <main class="container mx-auto px-4 py-8">
            <!-- Team Display Section -->
            <div class="bg-white rounded-lg border-4 border-blue-800 p-6 mb-8">
                <h2 class="text-red-600 text-xl md:text-2xl mb-6 text-center">YOUR TEAM</h2>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4 mb-8" id="team-slots">
                    @for($i = 1; $i <= 6; $i++)
                        <div class="bg-gray-100 rounded-lg p-2 border-2 border-gray-300 hover:border-yellow-400 transition-colors text-center cursor-pointer"
                             data-slot="{{ $i }}">
                            @if(isset($team->pokemon[$i-1]))
                                @php $pokemon = $team->pokemon[$i-1]; @endphp
                                <img src="{{ $pokemon->sprite_url }}"
                                     class="w-full h-24 object-contain"
                                     alt="{{ $pokemon->name }}">
                                <p class="text-xs mt-2 text-gray-800">{{ strtoupper($pokemon->name) }}</p>
                                <button class="remove-pokemon mt-1 text-red-600 hover:text-red-800 text-xxs"
                                        data-pokemon-id="{{ $pokemon->id }}"
                                        data-slot="{{ $i }}">
                                    REMOVE
                                </button>
                            @else
                                <div class="h-24 flex items-center justify-center">
                                    <p class="text-gray-400 text-xs">SLOT #{{ $i }}</p>
                                </div>
                            @endif
                        </div>
                    @endfor
                </div>
            </div>

            <!-- Search Section -->
            <div class="bg-white rounded-lg border-4 border-blue-800 p-4 mb-8">
                <div class="mb-6">
                    <input type="text"
                           id="pokemonSearch"
                           placeholder="SEARCH POKÉMON..."
                           class="w-full p-3 border-2 border-red-600 rounded-lg text-sm focus:outline-none focus:border-yellow-400"
                           style="font-family: 'Press Start 2P', cursive;">
                </div>

                <!-- Search Results Grid -->
                <div class="pokedex-grid" id="searchResults">
                    <!-- Results will appear here -->
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-center space-x-4">
                <button id="saveTeam"
                        class="bg-red-600 hover:bg-red-700 text-white py-3 px-6 rounded-lg inline-block border-2 border-black shadow-lg transform hover:scale-105 transition-transform">
                    SAVE TEAM
                </button>
                <button id="clearTeam"
                        class="bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-lg inline-block border-2 border-black shadow-lg transform hover:scale-105 transition-transform">
                    CLEAR TEAM
                </button>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('pokemonSearch');
            const searchResults = document.getElementById('searchResults');
            const teamSlots = document.getElementById('team-slots');
            const saveButton = document.getElementById('saveTeam');
            const clearButton = document.getElementById('clearTeam');

            searchInput.addEventListener('input', debounce(function(e) {
                const query = e.target.value.trim();

                if (query.length < 2) {
                    searchResults.innerHTML = '';
                    return;
                }

                fetch(`/pokemon/search?q=${encodeURIComponent(query)}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Network error');
                        return response.json();
                    })
                    .then(pokemonList => {
                        searchResults.innerHTML = pokemonList.map(pokemon => `
                <div class="pokemon-card" data-pokemon-id="${pokemon.id}">
                    <img src="${pokemon.sprite_url}"
                         class="pixel-sprite"
                         alt="${pokemon.name}">
                    <p class="text-xs mt-1">${pokemon.name.toUpperCase()}</p>
                    <p class="text-xxs">#${pokemon.pokeapi_id.toString().padStart(3, '0')}</p>
                </div>
            `).join('');

                        // Add click handlers
                        document.querySelectorAll('.pokemon-card').forEach(card => {
                            card.addEventListener('click', function() {
                                const pokemonId = this.getAttribute('data-pokemon-id');
                                addPokemonToTeam(pokemonId);
                            });
                        });
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        searchResults.innerHTML = '<p class="text-red-500">Search failed</p>';
                    });
            }, 300));

            function addPokemonToTeam(pokemonId) {
                // Find first empty slot (div without an img child)
                const emptySlot = [...document.querySelectorAll('#team-slots > div')]
                    .find(slot => !slot.querySelector('img[src*=".png"]'));

                if (!emptySlot) {
                    alert('Your team is already full! Remove a Pokémon first.');
                    return;
                }

                const slotNumber = emptySlot.getAttribute('data-slot');

                fetch(`/teams/{{ $team->id }}/add-pokemon`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        pokemon_id: pokemonId,
                        position: slotNumber
                    })
                })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw err; });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            alert(data.message || 'Error adding Pokémon to team');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert(error.message || 'Failed to add Pokémon');
                    });
            }

            // Remove Pokémon from team
            document.querySelectorAll('.remove-pokemon').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const pokemonId = this.getAttribute('data-pokemon-id');

                    if (confirm('Remove this Pokémon from your team?')) {
                        fetch(`/teams/{{ $team->id }}/remove-pokemon/${pokemonId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    window.location.reload();
                                }
                            });
                    }
                });
            });

            // Clear team
            clearButton.addEventListener('click', function() {
                if (confirm('Clear your entire team?')) {
                    fetch(`/teams/{{ $team->id }}/clear`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.reload();
                            }
                        });
                }
            });

            // Save team
            saveButton.addEventListener('click', function() {
                fetch(`/teams/{{ $team->id }}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Team saved successfully!');
                        }
                    });
            });

            // Debounce function for search
            function debounce(func, wait) {
                let timeout;
                return function() {
                    const context = this, args = arguments;
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        func.apply(context, args);
                    }, wait);
                };
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('pokemonSearch');
            const searchResults = document.getElementById('searchResults');
            let allPokemon = []; // Will store our Pokémon data

            // Load all Pokémon initially (like Pokédex does)
            async function loadAllPokemon() {
                try {
                    searchResults.innerHTML = `
                <div class="col-span-full text-center py-10">
                    <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/items/poke-ball.png"
                         alt="Loading"
                         class="loading-pokeball mx-auto">
                    <p class="text-gray-600 mt-4 text-sm">LOADING POKÉMON...</p>
                </div>`;

                    // Fetch first 151 Pokémon from PokeAPI
                    const pokemonList = [];
                    for (let id = 1; id <= 151; id++) {
                        const response = await axios.get(`https://pokeapi.co/api/v2/pokemon/${id}`);
                        pokemonList.push({
                            id: response.data.id,
                            name: response.data.name,
                            sprite_url: `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/${response.data.id}.png`,
                            pokeapi_id: response.data.id
                        });
                    }

                    allPokemon = pokemonList;
                    displaySearchResults(allPokemon);

                } catch (error) {
                    console.error("Error loading Pokémon:", error);
                    searchResults.innerHTML = `
                <div class="col-span-full text-center py-10">
                    <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/54.png"
                         alt="Error"
                         class="w-16 h-16 mx-auto">
                    <p class="text-red-600 mt-4 text-sm">ERROR LOADING POKÉMON!</p>
                </div>`;
                }
            }

            // Display results in the grid
            function displaySearchResults(pokemonList) {
                searchResults.innerHTML = '';

                pokemonList.sort((a, b) => a.id - b.id).forEach(pokemon => {
                    const card = document.createElement('div');
                    card.className = 'pokemon-card';
                    card.innerHTML = `
                <img src="${pokemon.sprite_url}"
                     alt="${pokemon.name}"
                     class="pixel-sprite">
                <p class="text-gray-800 text-xs mt-2">${pokemon.name.toUpperCase()}</p>
                <p class="text-gray-500 text-xxs">#${pokemon.id.toString().padStart(3, '0')}</p>
            `;
                    card.addEventListener('click', () => {
                        addPokemonToTeam(pokemon.id);
                    });
                    searchResults.appendChild(card);
                });
            }

            // Search functionality (client-side filtering)
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();

                if (!searchTerm) {
                    displaySearchResults(allPokemon);
                    return;
                }

                const filtered = allPokemon.filter(pokemon =>
                    pokemon.name.toLowerCase().includes(searchTerm) ||
                    pokemon.id.toString().includes(searchTerm)
                );

                displaySearchResults(filtered);
            });

            // Keep your existing addPokemonToTeam function
            function addPokemonToTeam(pokemonId) {
                const emptySlot = document.querySelector('.team-slot:not(:has(img))');

                if (!emptySlot) {
                    alert('Your team is already full! Remove a Pokémon first.');
                    return;
                }

                const slotNumber = emptySlot.getAttribute('data-slot');
                const pokemon = allPokemon.find(p => p.id == pokemonId);

                fetch(`/teams/{{ $team->id }}/add-pokemon`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        pokemon_id: pokemonId,
                        position: slotNumber,
                        name: pokemon.name,
                        sprite_url: pokemon.sprite_url
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            alert(data.message || 'Error adding Pokémon to team');
                        }
                    });
            }

            // Initialize
            loadAllPokemon();

            // Keep your existing remove/clear/save functions
            // ...
        });
    </script>

    <style>
        .text-shadow {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        .transition-transform {
            transition: transform 0.2s ease-in-out;
        }
    </style>
@endsection
